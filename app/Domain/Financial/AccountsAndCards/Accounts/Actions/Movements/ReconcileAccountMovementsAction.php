<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateEntriesAccountIdAction;
use Carbon\Carbon;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountByIdAction;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\BankStatementExtractorFactory;
use Domain\Financial\AccountsAndCards\Accounts\Services\BankStatements\Extractors\CaixaStatementExtractor;
use Domain\Financial\Entries\Cults\Actions\GetCultsAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

    /**
     * Dados da conta sendo processada (disponível para toda a classe)
     */
    private ?AccountData $account = null;

    public function __construct(
        private readonly AccountMovementsRepositoryInterface $accountMovementsRepository,
        private readonly GetEntriesAction $getEntriesAction,
        private readonly GetCultsAction $getCultsAction,
        private readonly GetExitsAction $getExitsAction,
        private readonly UpdateEntriesAccountIdAction $updateEntriesAccountIdAction,
        private readonly GetAccountByIdAction $getAccountByIdAction,
        private readonly BankStatementExtractorFactory $extractorFactory
    ) {}

    /**
     * Execute reconciliation of account movements
     * @throws \Throwable
     */
    public function execute(int $accountId, int $fileId): bool
    {
        // 1. Buscar dados da conta (disponível para toda a classe)
        $this->account = $this->getAccountByIdAction->execute($accountId);

        // 2. Buscar movimentos do arquivo
        $movements = $this->accountMovementsRepository->getMovementsByAccountAndFile($accountId, $fileId);

        if ($movements->isEmpty()) {
            return false;
        }

        // 3. Extrair período de datas dos movimentos
        $dateRange = $this->extractDateRange($movements);

        // Extrair apenas o formato YYYY-MM para busca com LIKE (entries, cults e exits usam LIKE)
        $yearMonthDates = $this->extractYearMonthFromDates($dateRange);

        // 4. Buscar dados necessários usando Actions
        $entries = $this->getEntriesAction->execute($yearMonthDates, [self::FIELD_ACCOUNT_ID => $accountId], false);
        $cults = $this->getCultsAction->execute(false, $yearMonthDates);
        $exits = $this->getExitsAction->execute($yearMonthDates, [self::FIELD_ACCOUNT_ID => $accountId], false);

        // 5. Processar conciliação
        $reconciliationResult = $this->processReconciliation($movements, $entries, $cults, $exits, $accountId);

        // 6. Atualizar status dos movimentos em massa
        $this->accountMovementsRepository->bulkUpdateConciliationStatus($reconciliationResult['movements']);

        // 7. Atualizar account_id das entradas conciliadas (segunda camada de identificação)
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

        // Inicializar todos os movimentos como não encontrados
        foreach ($movements as $movement) {
            $reconciliationMap[$movement->{self::FIELD_ID}] = self::STATUS_MOVEMENT_NOT_FOUND;
        }

        // FASE 1: Conciliar CULTOS (Culto → Extrato)
        // Itera pelos cultos e busca os depósitos correspondentes no extrato
        $cultsResult = $this->reconcileCults($cults, $movements, $reconciliationMap);
        $reconciliationMap = $cultsResult['reconciliation_map'];
        $entriesToUpdate = array_merge($entriesToUpdate, $cultsResult['entries_to_update']);

        // FASE 2: Conciliar ENTRADAS PIX/DOC (Extrato → Entradas)
        // Itera pelos movimentos de crédito ainda não conciliados
        $uniqueEntries = $entries->whereIn(self::FIELD_TRANSACTION_TYPE, [
            self::TRANSACTION_TYPE_PIX,
            self::TRANSACTION_TYPE_DOC_TED_TEV
        ])->whereNull(self::FIELD_CULT_ID);

        $entryUsageCount = [];
        foreach ($movements as $movement) {
            // Só processar se ainda não foi conciliado
            if ($reconciliationMap[$movement->{self::FIELD_ID}] === self::STATUS_MOVEMENT_NOT_FOUND
                && $movement->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_CREDIT) {

                $result = $this->reconcileUniqueEntry($movement, $uniqueEntries, $entryUsageCount);

                if ($result[self::RESULT_KEY_STATUS] === self::STATUS_CONCILIATED) {
                    $reconciliationMap[$movement->{self::FIELD_ID}] = self::STATUS_CONCILIATED;
                    $entriesToUpdate[] = $result[self::RESULT_KEY_ENTRY_ID];
                }
            }
        }

        // FASE 3: Conciliar SAÍDAS (Extrato → Saídas)
        $exitUsageCount = [];
        foreach ($movements as $movement) {
            // Só processar se ainda não foi conciliado
            if ($reconciliationMap[$movement->{self::FIELD_ID}] === self::STATUS_MOVEMENT_NOT_FOUND
                && $movement->{self::FIELD_MOVEMENT_TYPE} === self::MOVEMENT_TYPE_DEBIT) {

                $result = $this->reconcileExit($movement, $exits, $exitUsageCount);

                if ($result[self::RESULT_KEY_STATUS] === self::STATUS_CONCILIATED) {
                    $reconciliationMap[$movement->{self::FIELD_ID}] = self::STATUS_CONCILIATED;
                }
            }
        }

        return [
            'movements' => $reconciliationMap,
            'entries_to_update' => array_unique($entriesToUpdate),
        ];
    }

    /**
     * FASE 1: Conciliar cultos (Culto → Extrato)
     *
     * Estratégia: Agrupa cultos e depósitos por data de compensação e compara os totais.
     * Se a soma dos cultos de uma data bater com a soma dos depósitos em dinheiro
     * da mesma data, todos são conciliados.
     *
     * Isso resolve o problema de divisões arbitrárias de depósitos, onde a pessoa
     * pode depositar valores diferentes do esperado (ex: 1500+525 em vez de 2000+25).
     */
    private function reconcileCults(Collection $cults, Collection $movements, array $reconciliationMap): array
    {
        $entriesToUpdate = [];

        // Obter identificador de depósito em dinheiro do banco
        $cashDepositIdentifier = $this->getCashDepositIdentifier();

        // 1. Agrupar cultos por data de compensação
        $cultsByDate = $this->groupCultsByCompensationDate($cults);

        // 2. Agrupar depósitos em dinheiro por data
        $depositsByDate = $this->groupCashDepositsByDate($movements, $reconciliationMap, $cashDepositIdentifier);

        // 3. Para cada data, comparar totais e conciliar
        foreach ($cultsByDate as $date => $cultsData) {
            $totalCultsAmount = $cultsData['total'];
            $cultsList = $cultsData['cults'];

            // Verificar se existem depósitos para esta data
            if (! isset($depositsByDate[$date])) {
                continue;
            }

            $depositsData = $depositsByDate[$date];
            $totalDepositsAmount = $depositsData['total'];
            $depositMovements = $depositsData['movements'];

            // Se os totais batem (dentro da tolerância), conciliar todos
            if (abs($totalCultsAmount - $totalDepositsAmount) < self::AMOUNT_TOLERANCE) {
                // Marcar todos os depósitos como conciliados
                foreach ($depositMovements as $movement) {
                    $reconciliationMap[$movement->{self::FIELD_ID}] = self::STATUS_CONCILIATED;
                }

                // Adicionar todas as entradas dos cultos para atualização
                foreach ($cultsList as $cult) {
                    $cultEntryIds = collect($cult->{self::FIELD_ENTRIES} ?? [])->pluck('id')->filter()->values()->toArray();
                    $entriesToUpdate = array_merge($entriesToUpdate, $cultEntryIds);
                }
            }
        }

        return [
            'reconciliation_map' => $reconciliationMap,
            'entries_to_update' => $entriesToUpdate,
        ];
    }

    /**
     * Agrupa cultos por data de compensação e calcula o total de cada grupo
     *
     * @return array<string, array{total: float, cults: array}>
     */
    private function groupCultsByCompensationDate(Collection $cults): array
    {
        $grouped = [];

        foreach ($cults as $cult) {
            $date = Carbon::parse($cult->{self::FIELD_CULTS_DATE_TRANSACTION_COMPENSATION})->format('Y-m-d');

            // Calcular valor total do culto
            $cultAmount = ($cult->{self::FIELD_CULTS_TITHES_AMOUNT} ?? 0)
                + ($cult->{self::FIELD_CULTS_DESIGNATED_AMOUNT} ?? 0)
                + ($cult->{self::FIELD_CULTS_OFFER_AMOUNT} ?? 0);

            if (! isset($grouped[$date])) {
                $grouped[$date] = [
                    'total' => 0,
                    'cults' => [],
                ];
            }

            $grouped[$date]['total'] += $cultAmount;
            $grouped[$date]['cults'][] = $cult;
        }

        return $grouped;
    }

    /**
     * Agrupa depósitos em dinheiro (DIN) por data e calcula o total de cada grupo
     *
     * @return array<string, array{total: float, movements: array}>
     */
    private function groupCashDepositsByDate(
        Collection $movements,
        array $reconciliationMap,
        ?string $cashDepositIdentifier = null
    ): array {
        $grouped = [];

        foreach ($movements as $movement) {
            // Só considerar se ainda não foi conciliado
            if ($reconciliationMap[$movement->{self::FIELD_ID}] !== self::STATUS_MOVEMENT_NOT_FOUND) {
                continue;
            }

            // Verificar se é crédito
            if ($movement->{self::FIELD_MOVEMENT_TYPE} !== self::MOVEMENT_TYPE_CREDIT) {
                continue;
            }

            // Verificar se é depósito em dinheiro
            if ($cashDepositIdentifier !== null) {
                if (stripos($movement->transactionType, $cashDepositIdentifier) === false) {
                    continue;
                }
            }

            $date = $movement->{self::FIELD_MOVEMENT_DATE};

            if (! isset($grouped[$date])) {
                $grouped[$date] = [
                    'total' => 0,
                    'movements' => [],
                ];
            }

            $grouped[$date]['total'] += $movement->{self::FIELD_AMOUNT};
            $grouped[$date]['movements'][] = $movement;
        }

        return $grouped;
    }

    /**
     * FASE 2: Conciliar entrada PIX/DOC única (1:1)
     */
    private function reconcileUniqueEntry(object $movement, Collection $uniqueEntries, array &$entryUsageCount): array
    {
        $movementDate = $movement->{self::FIELD_MOVEMENT_DATE};
        $movementAmount = $movement->{self::FIELD_AMOUNT};

        $entry = $uniqueEntries->first(function ($e) use ($movementDate, $movementAmount, $entryUsageCount) {
            $entryDate = Carbon::parse($e->{self::FIELD_ENTRIES_DATE_TRANSACTION_COMPENSATION})->format('Y-m-d');
            $entryId = $e->{self::FIELD_ENTRIES_ID};

            // Verificar se já foi conciliada
            if (isset($entryUsageCount[$entryId]) && $entryUsageCount[$entryId] >= 1) {
                return false;
            }

            return $entryDate === $movementDate &&
                abs($e->{self::FIELD_ENTRIES_AMOUNT} - $movementAmount) < self::AMOUNT_TOLERANCE;
        });

        if ($entry) {
            $entryId = $entry->{self::FIELD_ENTRIES_ID};

            // Marcar como usada
            if (!isset($entryUsageCount[$entryId])) {
                $entryUsageCount[$entryId] = 0;
            }
            $entryUsageCount[$entryId]++;

            return [
                self::RESULT_KEY_STATUS => self::STATUS_CONCILIATED,
                self::RESULT_KEY_ENTRY_ID => $entryId,
            ];
        }

        return [self::RESULT_KEY_STATUS => self::STATUS_MOVEMENT_NOT_FOUND];
    }

    /**
     * FASE 3: Conciliar saída (1:1)
     */
    private function reconcileExit(object $movement, Collection $exits, array &$exitUsageCount): array
    {
        $movementDate = $movement->{self::FIELD_MOVEMENT_DATE};

        $exit = $exits->first(function ($e) use ($movementDate, $movement, $exitUsageCount) {
            $exitDate = Carbon::parse($e->{ExitData::DATE_TRANSACTION_COMPENSATION_ITEM_PROPERTY})->format('Y-m-d');
            $exitId = $e->{ExitData::ID_PROPERTY};

            // Verificar se já foi conciliada
            if (isset($exitUsageCount[$exitId]) && $exitUsageCount[$exitId] >= 1) {
                return false;
            }

            return $exitDate === $movementDate &&
                abs($e->{ExitData::AMOUNT_PROPERTY} - abs($movement->{self::FIELD_AMOUNT})) < self::AMOUNT_TOLERANCE;
        });

        if ($exit) {
            $exitId = $exit->{ExitData::ID_PROPERTY};

            // Marcar como usada
            if (!isset($exitUsageCount[$exitId])) {
                $exitUsageCount[$exitId] = 0;
            }
            $exitUsageCount[$exitId]++;

            return [self::RESULT_KEY_STATUS => self::STATUS_CONCILIATED];
        }

        return [self::RESULT_KEY_STATUS => self::STATUS_MOVEMENT_NOT_FOUND];
    }

    /**
     * Obtém o identificador de depósito em dinheiro do banco
     * Usa os dados da conta armazenados na propriedade $account
     *
     * @return string|null
     */
    private function getCashDepositIdentifier(): ?string
    {
        if ($this->account === null) {
            return null;
        }

        // Normalizar nome do banco
        $bankName = Str::lower(Str::ascii($this->account->bankName));

        // Criar extrator do banco usando a factory
        $extractor = $this->extractorFactory->make($bankName);

        // Se for Caixa, retornar o identificador de depósito em dinheiro
        if ($extractor instanceof CaixaStatementExtractor) {
            return CaixaStatementExtractor::TXT_CASH_DEPOSIT_IDENTIFIER;
        }

        // Para outros bancos, retornar null (sem verificação específica)
        return null;
    }
}
