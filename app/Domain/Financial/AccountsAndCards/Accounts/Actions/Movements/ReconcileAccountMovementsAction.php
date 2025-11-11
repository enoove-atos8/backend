<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Domain\Financial\Entries\Cults\Actions\GetCultsAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Illuminate\Support\Collection;

class ReconcileAccountMovementsAction
{
    // Status de conciliação
    private const STATUS_CONCILIATED = 'conciliated';

    private const STATUS_MOVEMENT_NOT_FOUND = 'not_found';

    // Tipos de movimento
    private const MOVEMENT_TYPE_CREDIT = 'credit';

    private const MOVEMENT_TYPE_DEBIT = 'debit';

    // Campos
    private const FIELD_ACCOUNT_ID = 'account_id';

    private const FIELD_CULT_ID = 'cult_id';

    private const FIELD_DATE_TRANSACTION_COMPENSATION = 'date_transaction_compensation';

    private const FIELD_AMOUNT = 'amount';

    private const FIELD_MOVEMENT_DATE = 'movementDate';

    private const FIELD_MOVEMENT_TYPE = 'movementType';

    private const FIELD_ID = 'id';

    private const FIELD_ENTRIES = 'entries';

    // Tolerância para comparação de valores decimais
    private const AMOUNT_TOLERANCE = 0.01;

    public function __construct(
        private readonly AccountMovementsRepositoryInterface $accountMovementsRepository,
        private readonly GetEntriesAction $getEntriesAction,
        private readonly GetCultsAction $getCultsAction,
        private readonly GetExitsAction $getExitsAction
    ) {}

    /**
     * Execute reconciliation of account movements
     * @throws \Throwable
     */
    public function execute(int $accountId, int $fileId): bool
    {
        // 1. Buscar movimentos do arquivo
        $movements = $this->accountMovementsRepository->getMovementsByAccountAndFile($accountId, $fileId);

        if ($movements->isEmpty()) {
            return false;
        }

        // 2. Extrair período de datas dos movimentos
        $dateRange = $this->extractDateRange($movements);
        $dates = $dateRange['start'].','.$dateRange['end'];

        // 3. Buscar dados necessários usando Actions
        $entries = $this->getEntriesAction->execute($dates, [self::FIELD_ACCOUNT_ID => $accountId], false);
        $cults = $this->getCultsAction->execute(false);
        $exits = $this->getExitsAction->execute($dates, [self::FIELD_ACCOUNT_ID => $accountId], false);

        // 4. Processar conciliação
        $reconciliationMap = $this->processReconciliation($movements, $entries, $cults, $exits, $dateRange);

        // 5. Atualizar status em massa
        $this->accountMovementsRepository->bulkUpdateConciliationStatus($reconciliationMap);

        return true;
    }

    private function extractDateRange(Collection $movements): array
    {
        $dates = $movements->pluck(self::FIELD_MOVEMENT_DATE)->unique()->sort()->values();

        return ['start' => $dates->first(), 'end' => $dates->last()];
    }

    private function processReconciliation(
        Collection $movements,
        Collection $entries,
        Collection $cults,
        Collection $exits,
        array $dateRange
    ): array {
        $reconciliationMap = [];

        // Filtrar entradas PIX (sem culto)
        $pixEntries = $entries->filter(fn ($e) => $e->{self::FIELD_CULT_ID} === null);

        // Filtrar cultos do período
        $cultsFiltered = $cults->filter(fn ($c) => $c->{self::FIELD_DATE_TRANSACTION_COMPENSATION} >= $dateRange['start'] &&
            $c->{self::FIELD_DATE_TRANSACTION_COMPENSATION} <= $dateRange['end']
        );

        foreach ($movements as $movement) {
            $status = self::STATUS_MOVEMENT_NOT_FOUND;

            if ($movement->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_CREDIT) {
                $status = $this->reconcileCredit($movement, $movements, $pixEntries, $cultsFiltered);
            } elseif ($movement->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_DEBIT) {
                $status = $this->reconcileDebit($movement, $exits);
            }

            $reconciliationMap[$movement->{self::FIELD_ID}] = $status;
        }

        return $reconciliationMap;
    }

    private function reconcileCredit($movement, Collection $allMovements, Collection $pixEntries, Collection $cults): string
    {
        // Verificar PIX (1:1)
        $pix = $pixEntries->first(fn ($e) => $e->{self::FIELD_DATE_TRANSACTION_COMPENSATION} === $movement->{self::FIELD_MOVEMENT_DATE} &&
            abs($e->{self::FIELD_AMOUNT} - $movement->{self::FIELD_AMOUNT}) < self::AMOUNT_TOLERANCE
        );

        if ($pix) {
            return self::STATUS_CONCILIATED;
        }

        // Verificar depósito em dinheiro (agregado por cultos)
        $depositsOfDay = $allMovements->filter(fn ($m) => $m->{self::FIELD_MOVEMENT_DATE} === $movement->{self::FIELD_MOVEMENT_DATE} &&
            $m->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_CREDIT
        );

        $totalDeposits = $depositsOfDay->sum(self::FIELD_AMOUNT);

        $cultsOfDay = $cults->filter(fn ($c) => $c->{self::FIELD_DATE_TRANSACTION_COMPENSATION} === $movement->{self::FIELD_MOVEMENT_DATE}
        );

        if ($cultsOfDay->isEmpty()) {
            return self::STATUS_MOVEMENT_NOT_FOUND;
        }

        // Somar entradas dos cultos (já vem na propriedade 'entries' do culto)
        $totalCultEntries = $cultsOfDay->sum(fn ($cult) => collect($cult->{self::FIELD_ENTRIES} ?? [])->sum(self::FIELD_AMOUNT)
        );

        if (abs($totalDeposits - $totalCultEntries) < self::AMOUNT_TOLERANCE) {
            return self::STATUS_CONCILIATED;
        }

        return self::STATUS_MOVEMENT_NOT_FOUND;
    }

    private function reconcileDebit($movement, Collection $exits): string
    {
        $exit = $exits->first(fn ($e) => $e->{self::FIELD_DATE_TRANSACTION_COMPENSATION} === $movement->{self::FIELD_MOVEMENT_DATE} &&
            abs($e->{self::FIELD_AMOUNT} - abs($movement->{self::FIELD_AMOUNT})) < self::AMOUNT_TOLERANCE
        );

        return $exit ? self::STATUS_CONCILIATED : self::STATUS_MOVEMENT_NOT_FOUND;
    }
}
