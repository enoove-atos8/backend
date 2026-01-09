<?php

namespace App\Application\Core\Jobs\WhatsApp\Ecclesiastical\Groups\AmountRequests;

use Domain\Ecclesiastical\Groups\AmountRequests\Actions\GetReminderByIdAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Actions\UpdateReminderStatusAction;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReminderConstants;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReminderData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Domain\Ecclesiastical\Groups\AmountRequests\Services\WhatsAppMessageTemplateService;
use Domain\Secretary\Membership\Actions\GetMemberByIdAction;
use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Infrastructure\Services\External\WhatsApp\Interfaces\WhatsAppServiceInterface;

class ProcessWhatsAppReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Job configuration
    private const MAX_TRIES = 3;

    private const BACKOFF_SECONDS = 60;

    private const COUNTRY_CODE_BRAZIL = '55';

    // Message metadata keys
    private const METADATA_KEY_MESSAGE_ID = 'message_id';

    // Database tables and columns
    private const TABLE_GROUPS = 'ecclesiastical_divisions_groups';

    private const COLUMN_GROUP_ID = 'id';

    private const COLUMN_GROUP_NAME = 'name';

    /**
     * The number of times the job may be attempted.
     */
    public $tries = self::MAX_TRIES;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = self::BACKOFF_SECONDS;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reminderId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        GetReminderByIdAction $getReminderByIdAction,
        GetMemberByIdAction $getMemberByIdAction,
        UpdateReminderStatusAction $updateReminderStatusAction,
        AmountRequestRepositoryInterface $amountRequestRepository,
        WhatsAppServiceInterface $whatsAppService,
        WhatsAppMessageTemplateService $templateService
    ): void {
        try {
            // Busca o reminder
            $reminder = $getReminderByIdAction->execute($this->reminderId);

            if (! $reminder) {
                Log::error('Reminder não encontrado', [
                    'reminder_id' => $this->reminderId,
                ]);

                return;
            }

            // Se já foi enviado, ignora
            if ($this->isAlreadySent($reminder)) {
                Log::info('Reminder já enviado', [
                    'reminder_id' => $this->reminderId,
                    'status' => $reminder->status,
                ]);

                return;
            }

            // Busca dados da solicitação
            $amountRequest = $amountRequestRepository->getById($reminder->amountRequestId);

            if (! $amountRequest) {
                $this->markAsFailedWithAction(
                    $updateReminderStatusAction,
                    'Solicitação não encontrada'
                );

                return;
            }

            // Busca dados do membro
            $member = $getMemberByIdAction->execute($amountRequest->memberId);

            if (! $member) {
                $this->markAsFailedWithAction(
                    $updateReminderStatusAction,
                    'Membro não encontrado'
                );

                return;
            }

            // Valida telefone
            if (! $this->hasValidPhone($member)) {
                $this->markAsFailedWithAction(
                    $updateReminderStatusAction,
                    'Membro sem telefone cadastrado'
                );

                return;
            }

            // Monta a mensagem usando o template
            $message = $this->buildMessage($reminder->type, $amountRequest, $member, $templateService);

            // Envia via WhatsApp
            $response = $whatsAppService->sendTextMessage(
                $this->formatPhoneNumber($member->cellPhone),
                $message
            );

            // Marca como enviado
            $metadata = array_merge(
                $reminder->metadata ?? [],
                [self::METADATA_KEY_MESSAGE_ID => $response['message_id'] ?? null]
            );

            $updateReminderStatusAction->execute(
                $this->reminderId,
                ReminderConstants::STATUS_SENT,
                null,
                $metadata
            );

            Log::info('Mensagem WhatsApp enviada com sucesso', [
                'reminder_id' => $this->reminderId,
                'amount_request_id' => $reminder->amountRequestId,
                'phone' => $member->cellPhone,
                'message_id' => $response['message_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            $this->markAsFailedWithAction(
                $updateReminderStatusAction ?? null,
                $e->getMessage()
            );
            throw $e;
        }
    }

    /**
     * Check if reminder was already sent
     */
    private function isAlreadySent(AmountRequestReminderData $reminder): bool
    {
        return in_array($reminder->status, [
            ReminderConstants::STATUS_SENT,
            ReminderConstants::STATUS_DELIVERED,
            ReminderConstants::STATUS_READ,
        ]);
    }

    /**
     * Check if member has valid phone
     */
    private function hasValidPhone(MemberData $member): bool
    {
        return ! empty($member->cellPhone);
    }

    /**
     * Build message from template
     */
    private function buildMessage(string $type, $amountRequest, MemberData $member, WhatsAppMessageTemplateService $templateService): string
    {
        // Busca nome do grupo
        $groupName = 'Grupo não identificado';
        if ($amountRequest->groupId) {
            $group = DB::table(self::TABLE_GROUPS)
                ->where(self::COLUMN_GROUP_ID, $amountRequest->groupId)
                ->first();

            if ($group) {
                $groupName = $group->{self::COLUMN_GROUP_NAME};
            }
        }

        // Calcula dias restantes até o prazo
        $daysRemaining = null;
        $pendingAmount = null;

        if ($amountRequest->proofDeadline) {
            $deadline = \Carbon\Carbon::parse($amountRequest->proofDeadline);
            $daysRemaining = now()->diffInDays($deadline, false);
        }

        if ($amountRequest->requestedAmount && $amountRequest->provenAmount) {
            $pendingAmount = (float) $amountRequest->requestedAmount - (float) $amountRequest->provenAmount;
        }

        // Prepara os dados para substituição no template
        $data = [
            'member_name' => $this->getFirstName($member->fullName),
            'group_name' => $groupName,
            'amount' => number_format((float) $amountRequest->requestedAmount, 2, ',', '.'),
            'description' => $amountRequest->description ?? '',
            'deadline' => $amountRequest->proofDeadline ? \Carbon\Carbon::parse($amountRequest->proofDeadline)->format('d/m/Y') : '',
            'rejection_reason' => $amountRequest->rejectionReason ?? '',
            'proven_amount' => number_format((float) ($amountRequest->provenAmount ?? 0), 2, ',', '.'),
            'pending_amount' => $pendingAmount ? number_format($pendingAmount, 2, ',', '.') : '0,00',
            'days_remaining' => $daysRemaining !== null ? abs($daysRemaining) : '',
            'devolution_amount' => number_format((float) ($amountRequest->devolutionAmount ?? 0), 2, ',', '.'),
        ];

        return $templateService->getTemplate($type, $data);
    }

    /**
     * Extract first name from full name
     */
    private function getFirstName(?string $fullName): string
    {
        if (empty($fullName)) {
            return 'Membro';
        }

        $parts = explode(' ', trim($fullName));

        return $parts[0];
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove todos os caracteres não numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Se não começar com código do país (55), adiciona
        if (! str_starts_with($phone, self::COUNTRY_CODE_BRAZIL)) {
            $phone = self::COUNTRY_CODE_BRAZIL.$phone;
        }

        return $phone;
    }

    /**
     * Mark reminder as failed using action
     */
    private function markAsFailedWithAction(?UpdateReminderStatusAction $action, string $errorMessage): void
    {
        if ($action) {
            $action->execute(
                $this->reminderId,
                ReminderConstants::STATUS_FAILED,
                $errorMessage
            );
        }

        Log::error('Falha ao enviar mensagem WhatsApp', [
            'reminder_id' => $this->reminderId,
            'error' => $errorMessage,
        ]);
    }
}
