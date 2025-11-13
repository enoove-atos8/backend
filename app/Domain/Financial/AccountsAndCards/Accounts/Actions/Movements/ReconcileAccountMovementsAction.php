<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateEntriesAccountIdAction;
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
    private const FIELD_ENTRIES_ID = 'entries_id';

    private const FIELD_ENTRIES_DATE_TRANSACTION_COMPENSATION = 'entries_date_transaction_compensation';

    private const FIELD_ENTRIES_AMOUNT = 'entries_amount';

    private const FIELD_CULTS_ID = 'cults_id';

    private const FIELD_CULTS_DATE_TRANSACTION_COMPENSATION = 'cults_date_transaction_compensation';

    private const FIELD_CULTS_TITHES_AMOUNT = 'cults_tithes_amount';

    private const FIELD_CULTS_DESIGNATED_AMOUNT = 'cults_designated_amount';

    private const FIELD_CULTS_OFFER_AMOUNT = 'cults_offer_amount';

    private const FIELD_EXITS_ID = 'exits_id';

    private const FIELD_EXITS_DATE_TRANSACTION_COMPENSATION = 'exits_date_transaction_compensation';

    private const FIELD_EXITS_AMOUNT = 'exits_amount';

    // Chaves para resultado de conciliação
    private const RESULT_KEY_STATUS = 'status';

    private const RESULT_KEY_ENTRY_ID = 'entry_id';

    private const RESULT_KEY_CULT_ENTRY_IDS = 'cult_entry_ids';

    // Tipos de transação
    private const TRANSACTION_TYPE_PIX = 'pix';
    private const TRANSACTION_TYPE_DOC_TED_TEV = 'doc_ted_tev';

    // Tolerância para comparação de valores decimais
    private const AMOUNT_TOLERANCE = 0.01;

    // Limite máximo para depósitos em dinheiro
    private const MAX_CASH_DEPOSIT = 2000.00;

    public function __construct(
        private readonly AccountMovementsRepositoryInterface $accountMovementsRepository,
        private readonly GetEntriesAction $getEntriesAction,
        private readonly GetCultsAction $getCultsAction,
        private readonly GetExitsAction $getExitsAction,
        private readonly UpdateEntriesAccountIdAction $updateEntriesAccountIdAction
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
        $reconciliationResult = $this->processReconciliation($movements, $entries, $cults, $exits, $accountId);

        // 5. Atualizar status dos movimentos em massa
        $this->accountMovementsRepository->bulkUpdateConciliationStatus($reconciliationResult['movements']);

        // 6. Atualizar account_id das entradas conciliadas (segunda camada de identificação)
        if (!empty($reconciliationResult['entries_to_update'])) {
            $this->updateEntriesAccountIdAction->execute($reconciliationResult['entries_to_update'], $accountId);
        }

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
        Collection $exits,
        int $accountId
    ): array {
        $reconciliationMap = [];
        $entriesToUpdate = [];

        // Contadores para rastrear quantas vezes cada item foi conciliado
        $entryUsageCount = []; // ['entry_id' => count]
        $cultUsageCount = []; // ['cult_id' => count]
        $exitUsageCount = []; // ['exit_id' => count]

        // Filtrar entradas únicas: PIX e DOC/TED/TEV (sem culto)
        $uniqueEntries = $entries->whereIn(self::FIELD_TRANSACTION_TYPE, [
            self::TRANSACTION_TYPE_PIX,
            self::TRANSACTION_TYPE_DOC_TED_TEV
        ])->whereNull(self::FIELD_CULT_ID);

        foreach ($movements as $movement) {
            $status = self::STATUS_MOVEMENT_NOT_FOUND;

            if ($movement->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_CREDIT) {
                $result = $this->reconcileCredit($movement, $movements, $uniqueEntries, $cults, $entryUsageCount, $cultUsageCount);
                $status = $result[self::RESULT_KEY_STATUS];

                // Se conciliou uma entrada PIX, coletar ID para atualizar account_id
                if ($status === self::STATUS_CONCILIATED && isset($result[self::RESULT_KEY_ENTRY_ID])) {
                    $entriesToUpdate[] = $result[self::RESULT_KEY_ENTRY_ID];
                }

                // Se conciliou um culto, coletar IDs das entradas do culto para atualizar account_id
                if ($status === self::STATUS_CONCILIATED && isset($result[self::RESULT_KEY_CULT_ENTRY_IDS])) {
                    $entriesToUpdate = array_merge($entriesToUpdate, $result[self::RESULT_KEY_CULT_ENTRY_IDS]);
                }
            } elseif ($movement->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_DEBIT) {
                $result = $this->reconcileDebit($movement, $exits, $exitUsageCount);
                $status = $result[self::RESULT_KEY_STATUS];
            }

            $reconciliationMap[$movement->{self::FIELD_ID}] = $status;
        }

        return [
            'movements' => $reconciliationMap,
            'entries_to_update' => array_unique($entriesToUpdate),
        ];
    }

    private function reconcileCredit($movement, Collection $allMovements, Collection $pixEntries, Collection $cults, array &$entryUsageCount, array &$cultUsageCount): array
    {
        $movementDate = $movement->{self::FIELD_MOVEMENT_DATE};
        $movementAmount = $movement->{self::FIELD_AMOUNT};

        // Verificar PIX/DOC/TED/TEV (1:1 - cada entrada só pode conciliar 1 vez)
        $pix = $pixEntries->first(function ($e) use ($movementDate, $movementAmount, $entryUsageCount) {
            $entryDate = Carbon::parse($e->{self::FIELD_ENTRIES_DATE_TRANSACTION_COMPENSATION})->format('Y-m-d');
            $entryId = $e->{self::FIELD_ENTRIES_ID};

            // Verificar se já foi conciliada
            if (isset($entryUsageCount[$entryId]) && $entryUsageCount[$entryId] >= 1) {
                return false;
            }

            return $entryDate === $movementDate &&
                abs($e->{self::FIELD_ENTRIES_AMOUNT} - $movementAmount) < self::AMOUNT_TOLERANCE;
        });

        if ($pix) {
            $entryId = $pix->{self::FIELD_ENTRIES_ID};

            // Incrementar contador de uso
            if (!isset($entryUsageCount[$entryId])) {
                $entryUsageCount[$entryId] = 0;
            }
            $entryUsageCount[$entryId]++;

            return [
                self::RESULT_KEY_STATUS => self::STATUS_CONCILIATED,
                self::RESULT_KEY_ENTRY_ID => $entryId,
            ];
        }

        // Verificar depósito em dinheiro via culto
        $cult = $cults->first(function ($c) use ($movementDate, $movementAmount, $cultUsageCount) {
            $cultDate = Carbon::parse($c->{self::FIELD_CULTS_DATE_TRANSACTION_COMPENSATION})->format('Y-m-d');
            $cultId = $c->{self::FIELD_CULTS_ID};

            if ($cultDate !== $movementDate) {
                return false;
            }

            // Valor total do culto = dízimos + ofertas designadas + ofertas
            $totalCultAmount = ($c->{self::FIELD_CULTS_TITHES_AMOUNT} ?? 0)
                + ($c->{self::FIELD_CULTS_DESIGNATED_AMOUNT} ?? 0)
                + ($c->{self::FIELD_CULTS_OFFER_AMOUNT} ?? 0);

            // Calcular quantos depósitos esse culto deve gerar
            $expectedDeposits = $this->calculateExpectedDeposits($totalCultAmount);

            // Verificar se já atingiu o limite de conciliações
            $currentUsage = $cultUsageCount[$cultId] ?? 0;
            if ($currentUsage >= $expectedDeposits) {
                return false; // Já conciliou todos os depósitos esperados
            }

            // Se o culto tem <= R$ 2.000, verifica se bate com o movimento
            if ($totalCultAmount <= self::MAX_CASH_DEPOSIT) {
                return abs($totalCultAmount - $movementAmount) < self::AMOUNT_TOLERANCE;
            }

            // Se o culto tem > R$ 2.000, verifica se o movimento corresponde a uma das parcelas
            return $this->isValidCultDepositPart($totalCultAmount, $movementAmount);
        });

        if ($cult) {
            $cultId = $cult->{self::FIELD_CULTS_ID};

            // Incrementar contador de uso do culto
            if (!isset($cultUsageCount[$cultId])) {
                $cultUsageCount[$cultId] = 0;
            }
            $cultUsageCount[$cultId]++;

            // Extrair IDs das entradas do culto
            $cultEntryIds = collect($cult->{self::FIELD_ENTRIES} ?? [])->pluck('id')->filter()->values()->toArray();

            return [
                self::RESULT_KEY_STATUS => self::STATUS_CONCILIATED,
                self::RESULT_KEY_CULT_ENTRY_IDS => $cultEntryIds,
            ];
        }

        return [self::RESULT_KEY_STATUS => self::STATUS_MOVEMENT_NOT_FOUND];
    }

    /**
     * Calcula quantos depósitos um culto deve gerar baseado no valor total
     */
    private function calculateExpectedDeposits(float $totalCultAmount): int
    {
        if ($totalCultAmount <= self::MAX_CASH_DEPOSIT) {
            return 1;
        }

        $fullDeposits = floor($totalCultAmount / self::MAX_CASH_DEPOSIT);
        $remainder = $totalCultAmount - ($fullDeposits * self::MAX_CASH_DEPOSIT);

        // Se há resto, precisa de mais um depósito
        return $remainder > 0 ? $fullDeposits + 1 : $fullDeposits;
    }

    /**
     * Verifica se o valor do movimento corresponde a uma parcela válida de depósito do culto
     * Exemplo: Culto de R$ 9.000 gera 4 depósitos de R$ 2.000 e 1 de R$ 1.000
     */
    private function isValidCultDepositPart(float $totalCultAmount, float $movementAmount): bool
    {
        // Calcular quantos depósitos de R$ 2.000 são necessários
        $fullDeposits = floor($totalCultAmount / self::MAX_CASH_DEPOSIT);
        $remainder = $totalCultAmount - ($fullDeposits * self::MAX_CASH_DEPOSIT);

        // Verificar se o movimento é um depósito completo de R$ 2.000
        if (abs($movementAmount - self::MAX_CASH_DEPOSIT) < self::AMOUNT_TOLERANCE) {
            return true;
        }

        // Verificar se o movimento é o depósito restante (última parcela)
        if ($remainder > 0 && abs($movementAmount - $remainder) < self::AMOUNT_TOLERANCE) {
            return true;
        }

        return false;
    }

    private function reconcileDebit($movement, Collection $exits, array &$exitUsageCount): array
    {
        $movementDate = $movement->{self::FIELD_MOVEMENT_DATE};

        $exit = $exits->first(function ($e) use ($movementDate, $movement, $exitUsageCount) {
            $exitDate = Carbon::parse($e->{ExitData::DATE_TRANSACTION_COMPENSATION_ITEM_PROPERTY})->format('Y-m-d');
            $exitId = $e->{ExitData::ID_PROPERTY};

            // Verificar se já foi conciliada (1:1)
            if (isset($exitUsageCount[$exitId]) && $exitUsageCount[$exitId] >= 1) {
                return false;
            }

            return $exitDate === $movementDate &&
                abs($e->{ExitData::AMOUNT_PROPERTY} - abs($movement->{self::FIELD_AMOUNT})) < self::AMOUNT_TOLERANCE;
        });

        if ($exit) {
            $exitId = $exit->{ExitData::ID_PROPERTY};

            // Incrementar contador de uso
            if (!isset($exitUsageCount[$exitId])) {
                $exitUsageCount[$exitId] = 0;
            }
            $exitUsageCount[$exitId]++;

            return [self::RESULT_KEY_STATUS => self::STATUS_CONCILIATED];
        }

        return [self::RESULT_KEY_STATUS => self::STATUS_MOVEMENT_NOT_FOUND];
    }
}
