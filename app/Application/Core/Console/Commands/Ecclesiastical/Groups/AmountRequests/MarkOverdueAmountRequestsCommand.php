<?php

namespace App\Application\Core\Console\Commands\Ecclesiastical\Groups\AmountRequests;

use Application\Core\Events\Ecclesiastical\Groups\AmountRequests\AmountRequestStatusChanged;
use Carbon\Carbon;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkOverdueAmountRequestsCommand extends Command
{
    // Table and column constants
    private const TABLE_AMOUNT_REQUESTS = 'amount_requests';

    private const COLUMN_ID = 'id';

    private const COLUMN_STATUS = 'status';

    private const COLUMN_PROOF_DEADLINE = 'proof_deadline';

    private const COLUMN_DELETED = 'deleted';

    private const COLUMN_UPDATED_AT = 'updated_at';

    private const COLUMN_REQUESTED_AMOUNT = 'requested_amount';

    private const COLUMN_PROVEN_AMOUNT = 'proven_amount';

    private const COLUMN_GROUP_ID = 'group_id';

    private const COLUMN_MEMBER_ID = 'member_id';

    // History event
    private const HISTORY_EVENT_OVERDUE = 'overdue';

    private const HISTORY_DESCRIPTION_OVERDUE = 'Solicitação marcada como vencida por prazo expirado';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amount-requests:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca solicitações de verba como vencidas quando passam do prazo de comprovação';

    /**
     * Execute the console command.
     */
    public function handle(AmountRequestRepositoryInterface $repository): int
    {
        $this->info('Verificando solicitações vencidas...');

        $markedCount = 0;

        // Busca solicitações transferred ou partially_proven que passaram do prazo
        $overdueRequests = DB::table(self::TABLE_AMOUNT_REQUESTS)
            ->whereIn(self::COLUMN_STATUS, [
                ReturnMessages::STATUS_TRANSFERRED,
                ReturnMessages::STATUS_PARTIALLY_PROVEN,
            ])
            ->where(self::COLUMN_PROOF_DEADLINE, '<', Carbon::now()->toDateString())
            ->where(self::COLUMN_DELETED, false)
            ->get();

        $this->info("Solicitações vencidas encontradas: {$overdueRequests->count()}");

        foreach ($overdueRequests as $request) {
            try {
                $oldStatus = $request->{self::COLUMN_STATUS};

                // Atualiza status para overdue
                DB::table(self::TABLE_AMOUNT_REQUESTS)
                    ->where(self::COLUMN_ID, $request->{self::COLUMN_ID})
                    ->update([
                        self::COLUMN_STATUS => ReturnMessages::STATUS_OVERDUE,
                        self::COLUMN_UPDATED_AT => now(),
                    ]);

                // Registra histórico
                $repository->createHistory(new AmountRequestHistoryData(
                    amountRequestId: $request->{self::COLUMN_ID},
                    event: self::HISTORY_EVENT_OVERDUE,
                    description: self::HISTORY_DESCRIPTION_OVERDUE,
                    userId: 0, // Sistema
                    metadata: [
                        'proof_deadline' => $request->{self::COLUMN_PROOF_DEADLINE},
                        'old_status' => $oldStatus,
                    ]
                ));

                // Dispara evento para notificação
                event(new AmountRequestStatusChanged(
                    amountRequestId: $request->{self::COLUMN_ID},
                    oldStatus: $oldStatus,
                    newStatus: ReturnMessages::STATUS_OVERDUE,
                    userId: 0,
                    additionalData: [
                        'requested_amount' => $request->{self::COLUMN_REQUESTED_AMOUNT},
                        'proven_amount' => $request->{self::COLUMN_PROVEN_AMOUNT} ?? '0.00',
                        'proof_deadline' => $request->{self::COLUMN_PROOF_DEADLINE},
                        'group_id' => $request->{self::COLUMN_GROUP_ID},
                        'member_id' => $request->{self::COLUMN_MEMBER_ID},
                    ]
                ));

                $this->line("  → Solicitação #{$request->id} marcada como vencida");
                $markedCount++;
            } catch (\Exception $e) {
                $this->error("  ✗ Erro ao marcar solicitação #{$request->id}: {$e->getMessage()}");
            }
        }

        $this->info("Total de solicitações marcadas como vencidas: {$markedCount}");
        $this->info('Processamento concluído!');

        return Command::SUCCESS;
    }
}
