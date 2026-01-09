<?php

namespace App\Application\Core\Console\Commands\Ecclesiastical\Groups\AmountRequests;

use App\Application\Core\Jobs\WhatsApp\Ecclesiastical\Groups\AmountRequests\ProcessWhatsAppReminderJob;
use Carbon\Carbon;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendProofRemindersCommand extends Command
{
    // Table and column constants
    private const TABLE_AMOUNT_REQUESTS = 'amount_requests';

    private const TABLE_AMOUNT_REQUEST_REMINDERS = 'amount_request_reminders';

    private const COLUMN_ID = 'id';

    private const COLUMN_STATUS = 'status';

    private const COLUMN_PROOF_DEADLINE = 'proof_deadline';

    private const COLUMN_DELETED = 'deleted';

    private const COLUMN_AMOUNT_REQUEST_ID = 'amount_request_id';

    private const COLUMN_TYPE = 'type';

    private const COLUMN_CREATED_AT = 'created_at';

    // Reminder types
    private const REMINDER_TYPE_OVERDUE = 'proof_overdue';

    private const REMINDER_TYPE_URGENT = 'proof_urgent';

    private const REMINDER_TYPE_NORMAL = 'proof_reminder';

    // Reminder channels
    private const CHANNEL_WHATSAPP = 'whatsapp';

    // Reminder status
    private const REMINDER_STATUS_PENDING = 'pending';

    private const REMINDER_STATUS_FAILED = 'failed';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:send-proof-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia lembretes periódicos de comprovação de uso de verbas via WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle(AmountRequestRepositoryInterface $repository): int
    {
        $this->info('Iniciando envio de lembretes de comprovação...');

        $remindersCreated = 0;

        // 1. Busca solicitações com prazo vencido (overdue)
        $overdueRequests = $this->getOverdueRequests();
        $this->info("Solicitações vencidas encontradas: {$overdueRequests->count()}");

        foreach ($overdueRequests as $request) {
            if ($this->shouldSendReminder($request->{self::COLUMN_ID}, self::REMINDER_TYPE_OVERDUE, 24)) {
                $this->createReminder($repository, $request->{self::COLUMN_ID}, self::REMINDER_TYPE_OVERDUE);
                $remindersCreated++;
            }
        }

        // 2. Busca solicitações próximas do prazo (3 dias ou menos)
        $urgentRequests = $this->getUrgentRequests();
        $this->info("Solicitações urgentes encontradas: {$urgentRequests->count()}");

        foreach ($urgentRequests as $request) {
            if ($this->shouldSendReminder($request->{self::COLUMN_ID}, self::REMINDER_TYPE_URGENT, 24)) {
                $this->createReminder($repository, $request->{self::COLUMN_ID}, self::REMINDER_TYPE_URGENT);
                $remindersCreated++;
            }
        }

        // 3. Busca solicitações com prazo normal (7 a 4 dias)
        $normalRequests = $this->getNormalRequests();
        $this->info("Solicitações normais encontradas: {$normalRequests->count()}");

        foreach ($normalRequests as $request) {
            if ($this->shouldSendReminder($request->{self::COLUMN_ID}, self::REMINDER_TYPE_NORMAL, 72)) {
                $this->createReminder($repository, $request->{self::COLUMN_ID}, self::REMINDER_TYPE_NORMAL);
                $remindersCreated++;
            }
        }

        // 4. Busca solicitações parcialmente comprovadas (lembretes semanais)
        $partiallyProvenRequests = $this->getPartiallyProvenRequests();
        $this->info("Solicitações parcialmente comprovadas encontradas: {$partiallyProvenRequests->count()}");

        foreach ($partiallyProvenRequests as $request) {
            // Envia lembrete a cada 7 dias (168 horas)
            if ($this->shouldSendReminder($request->{self::COLUMN_ID}, self::REMINDER_TYPE_NORMAL, 168)) {
                $this->createReminder($repository, $request->{self::COLUMN_ID}, self::REMINDER_TYPE_NORMAL);
                $remindersCreated++;
            }
        }

        $this->info("Lembretes criados: {$remindersCreated}");
        $this->info('Processamento concluído!');

        return Command::SUCCESS;
    }

    /**
     * Get overdue requests (past deadline)
     */
    private function getOverdueRequests()
    {
        return DB::table(self::TABLE_AMOUNT_REQUESTS)
            ->where(self::COLUMN_STATUS, ReturnMessages::STATUS_TRANSFERRED)
            ->where(self::COLUMN_PROOF_DEADLINE, '<', Carbon::now()->toDateString())
            ->where(self::COLUMN_DELETED, false)
            ->get();
    }

    /**
     * Get urgent requests (3 days or less until deadline)
     */
    private function getUrgentRequests()
    {
        $today = Carbon::now()->toDateString();
        $urgentDate = Carbon::now()->addDays(3)->toDateString();

        return DB::table(self::TABLE_AMOUNT_REQUESTS)
            ->where(self::COLUMN_STATUS, ReturnMessages::STATUS_TRANSFERRED)
            ->whereBetween(self::COLUMN_PROOF_DEADLINE, [$today, $urgentDate])
            ->where(self::COLUMN_DELETED, false)
            ->get();
    }

    /**
     * Get normal requests (4 to 7 days until deadline)
     */
    private function getNormalRequests()
    {
        $startDate = Carbon::now()->addDays(4)->toDateString();
        $endDate = Carbon::now()->addDays(7)->toDateString();

        return DB::table(self::TABLE_AMOUNT_REQUESTS)
            ->where(self::COLUMN_STATUS, ReturnMessages::STATUS_TRANSFERRED)
            ->whereBetween(self::COLUMN_PROOF_DEADLINE, [$startDate, $endDate])
            ->where(self::COLUMN_DELETED, false)
            ->get();
    }

    /**
     * Get partially proven requests (need weekly reminders)
     */
    private function getPartiallyProvenRequests()
    {
        return DB::table(self::TABLE_AMOUNT_REQUESTS)
            ->where(self::COLUMN_STATUS, ReturnMessages::STATUS_PARTIALLY_PROVEN)
            ->where(self::COLUMN_PROOF_DEADLINE, '>=', Carbon::now()->toDateString())
            ->where(self::COLUMN_DELETED, false)
            ->get();
    }

    /**
     * Check if should send reminder (avoid spamming)
     *
     * @param  int  $minHoursInterval  Minimum hours since last reminder of this type
     */
    private function shouldSendReminder(int $amountRequestId, string $type, int $minHoursInterval = 24): bool
    {
        $lastReminder = DB::table(self::TABLE_AMOUNT_REQUEST_REMINDERS)
            ->where(self::COLUMN_AMOUNT_REQUEST_ID, $amountRequestId)
            ->where(self::COLUMN_TYPE, $type)
            ->where(self::COLUMN_STATUS, '!=', self::REMINDER_STATUS_FAILED)
            ->orderBy(self::COLUMN_CREATED_AT, 'desc')
            ->first();

        if (! $lastReminder) {
            return true;
        }

        $hoursSinceLastReminder = Carbon::parse($lastReminder->{self::COLUMN_CREATED_AT})->diffInHours(Carbon::now());

        return $hoursSinceLastReminder >= $minHoursInterval;
    }

    /**
     * Create reminder and dispatch job
     */
    private function createReminder(AmountRequestRepositoryInterface $repository, int $amountRequestId, string $type): void
    {
        try {
            $reminderId = $repository->createReminder(
                new AmountRequestReminderData(
                    amountRequestId: $amountRequestId,
                    type: $type,
                    channel: self::CHANNEL_WHATSAPP,
                    scheduledAt: now()->toDateTimeString(),
                    status: self::REMINDER_STATUS_PENDING
                )
            );

            ProcessWhatsAppReminderJob::dispatch($reminderId)
                ->onQueue('whatsapp');

            $this->line("  → Lembrete criado para solicitação #{$amountRequestId} (tipo: {$type})");
        } catch (\Exception $e) {
            $this->error("  ✗ Erro ao criar lembrete para solicitação #{$amountRequestId}: {$e->getMessage()}");
        }
    }
}
