<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use Carbon\Carbon;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Domain\Financial\Entries\Cults\Actions\GetCultsAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
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

    private const FIELD_CULT_ID = 'entries_cult_id';

    private const FIELD_DATE_TRANSACTION_COMPENSATION = 'date_transaction_compensation';

    private const FIELD_AMOUNT = 'amount';

    private const FIELD_MOVEMENT_DATE = 'movementDate';

    private const FIELD_MOVEMENT_TYPE = 'movementType';

    private const FIELD_ID = 'id';

    private const FIELD_ENTRIES = 'entries';

    private const FIELD_TRANSACTION_TYPE = 'entries_transaction_type';

    // Campos com aliases (retornados do banco)
    private const FIELD_ENTRIES_DATE_TRANSACTION_COMPENSATION = 'entries_date_transaction_compensation';

    private const FIELD_ENTRIES_AMOUNT = 'entries_amount';

    private const FIELD_CULTS_DATE_TRANSACTION_COMPENSATION = 'cults_date_transaction_compensation';

    private const FIELD_EXITS_DATE_TRANSACTION_COMPENSATION = 'exits_date_transaction_compensation';

    private const FIELD_EXITS_AMOUNT = 'exits_amount';

    // Tipos de transação
    private const TRANSACTION_TYPE_PIX = 'pix';

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

        // Extrair apenas o formato YYYY-MM para busca com LIKE (entries, cults e exits usam LIKE)
        $yearMonthDates = $this->extractYearMonthFromDates($dateRange);

        // 3. Buscar dados necessários usando Actions
        $entries = $this->getEntriesAction->execute($yearMonthDates, [self::FIELD_ACCOUNT_ID => $accountId], false);
        $cults = $this->getCultsAction->execute(false, $yearMonthDates);
        $exits = $this->getExitsAction->execute($yearMonthDates, [self::FIELD_ACCOUNT_ID => $accountId], false);

        // 4. Processar conciliação
        $reconciliationMap = $this->processReconciliation($movements, $entries, $cults, $exits);

        // 5. Atualizar status em massa
        $this->accountMovementsRepository->bulkUpdateConciliationStatus($reconciliationMap);

        return true;
    }

    private function extractDateRange(Collection $movements): array
    {
        $dates = $movements->pluck(self::FIELD_MOVEMENT_DATE)->unique()->sort()->values();

        return ['start' => $dates->first(), 'end' => $dates->last()];
    }

    private function extractYearMonthFromDates(array $dateRange): string
    {
        $startYearMonth = Carbon::parse($dateRange['start'])->format('Y-m');
        $endYearMonth = Carbon::parse($dateRange['end'])->format('Y-m');

        // Se for o mesmo mês, retorna apenas um
        if ($startYearMonth === $endYearMonth) {
            return $startYearMonth;
        }

        // Se for meses diferentes, retorna ambos separados por vírgula
        return $startYearMonth.','.$endYearMonth;
    }

    private function processReconciliation(
        Collection $movements,
        Collection $entries,
        Collection $cults,
        Collection $exits
    ): array {
        $reconciliationMap = [];

        // Filtrar entradas PIX (sem culto)
        $pixEntries = $entries->where(self::FIELD_TRANSACTION_TYPE, self::TRANSACTION_TYPE_PIX)->whereNull(self::FIELD_CULT_ID);

        foreach ($movements as $movement) {
            $status = self::STATUS_MOVEMENT_NOT_FOUND;

            if ($movement->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_CREDIT) {
                $status = $this->reconcileCredit($movement, $movements, $pixEntries, $cults);
            } elseif ($movement->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_DEBIT) {
                $status = $this->reconcileDebit($movement, $exits);
            }

            $reconciliationMap[$movement->{self::FIELD_ID}] = $status;
        }

        return $reconciliationMap;
    }

    private function reconcileCredit($movement, Collection $allMovements, Collection $pixEntries, Collection $cults): string
    {
        $movementDate = $movement->{self::FIELD_MOVEMENT_DATE};

        // Verificar PIX (1:1)
        $pix = $pixEntries->first(function ($e) use ($movementDate, $movement) {
            $entryDate = Carbon::parse($e->{self::FIELD_ENTRIES_DATE_TRANSACTION_COMPENSATION})->format('Y-m-d');
            return $entryDate === $movementDate &&
                abs($e->{self::FIELD_ENTRIES_AMOUNT} - $movement->{self::FIELD_AMOUNT}) < self::AMOUNT_TOLERANCE;
        });

        if ($pix) {
            return self::STATUS_CONCILIATED;
        }

        // Verificar depósito em dinheiro (agregado por cultos)
        $depositsOfDay = $allMovements->filter(fn ($m) => $m->{self::FIELD_MOVEMENT_DATE} === $movementDate &&
            $m->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_CREDIT
        );

        $totalDeposits = $depositsOfDay->sum(self::FIELD_AMOUNT);

        $cultsOfDay = $cults->filter(function ($c) use ($movementDate) {
            $cultDate = Carbon::parse($c->{self::FIELD_CULTS_DATE_TRANSACTION_COMPENSATION})->format('Y-m-d');
            return $cultDate === $movementDate;
        });

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
        $movementDate = $movement->{self::FIELD_MOVEMENT_DATE};

        $exit = $exits->first(function ($e) use ($movementDate, $movement) {
            $exitDate = Carbon::parse($e->{ExitData::DATE_TRANSACTION_COMPENSATION_ITEM_PROPERTY})->format('Y-m-d');
            return $exitDate === $movementDate &&
                abs($e->{ExitData::AMOUNT_PROPERTY} - abs($movement->{self::FIELD_AMOUNT})) < self::AMOUNT_TOLERANCE;
        });

        return $exit ? self::STATUS_CONCILIATED : self::STATUS_MOVEMENT_NOT_FOUND;
    }
}
