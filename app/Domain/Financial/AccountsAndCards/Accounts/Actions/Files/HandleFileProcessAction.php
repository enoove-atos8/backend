<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use Application\Core\Jobs\Financial\Accounts\ProcessAccountFileJob;
use Carbon\Carbon;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Movements\DeleteAccountMovementsAction;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountFilesRepository;

class HandleFileProcessAction
{
    private ChangeFileProcessingStatusAction $changeFileProcessingStatusAction;

    private AccountFileRepositoryInterface $accountFilesRepository;

    private AccountRepositoryInterface $accountRepository;

    private DeleteAccountMovementsAction $deleteAccountMovementsAction;

    private DeleteAnonymousEntriesAndExitsAction $deleteAnonymousEntriesAndExitsAction;

    private GetLastProcessedFileAction $getLastProcessedFileAction;

    private GetFutureProcessedFilesAction $getFutureProcessedFilesAction;

    private DeleteFutureProcessedDataAction $deleteFutureProcessedDataAction;

    public function __construct(
        ChangeFileProcessingStatusAction $changeFileProcessingStatusAction,
        AccountFileRepositoryInterface $accountFilesRepository,
        AccountRepositoryInterface $accountRepository,
        DeleteAccountMovementsAction $deleteAccountMovementsAction,
        DeleteAnonymousEntriesAndExitsAction $deleteAnonymousEntriesAndExitsAction,
        GetLastProcessedFileAction $getLastProcessedFileAction,
        GetFutureProcessedFilesAction $getFutureProcessedFilesAction,
        DeleteFutureProcessedDataAction $deleteFutureProcessedDataAction
    ) {
        $this->changeFileProcessingStatusAction = $changeFileProcessingStatusAction;
        $this->accountFilesRepository = $accountFilesRepository;
        $this->accountRepository = $accountRepository;
        $this->deleteAccountMovementsAction = $deleteAccountMovementsAction;
        $this->deleteAnonymousEntriesAndExitsAction = $deleteAnonymousEntriesAndExitsAction;
        $this->getLastProcessedFileAction = $getLastProcessedFileAction;
        $this->getFutureProcessedFilesAction = $getFutureProcessedFilesAction;
        $this->deleteFutureProcessedDataAction = $deleteFutureProcessedDataAction;
    }

    /**
     * @throws GeneralExceptions
     *
     * @return array|null Returns array if confirmation needed, null if processed
     */
    public function execute(
        int $fileId,
        string $processingType,
        string $tenant,
        bool $forceProcess = false,
        ?float $initialBalance = null,
        ?string $initialBalanceDate = null
    ): ?array {
        $file = $this->accountFilesRepository->getFilesById($fileId);

        // Verificar se existem meses futuros processados
        $futureFiles = $this->getFutureProcessedFilesAction->execute($file->accountId, $file->referenceDate);

        // Se existem meses futuros e não foi forçado, retornar para confirmação
        if ($futureFiles->isNotEmpty() && ! $forceProcess) {
            $formattedMonths = $this->formatMonthsForDisplay($futureFiles);
            $currentMonth = $this->formatSingleMonth($file->referenceDate);

            return [
                'requiresConfirmation' => true,
                'futureMonths' => $formattedMonths,
                'currentMonth' => $currentMonth,
            ];
        }

        // Verificar saldo inicial da conta
        $initialBalanceCheck = $this->checkInitialBalance($file->accountId, $file->referenceDate, $initialBalance, $initialBalanceDate);
        if ($initialBalanceCheck !== null) {
            return $initialBalanceCheck;
        }

        // Se foi informado um novo saldo inicial, atualizar a conta
        if ($initialBalance !== null && $initialBalanceDate !== null) {
            $this->accountRepository->updateInitialBalance($file->accountId, $initialBalance, $initialBalanceDate);
        }

        // Se for reprocessamento de um arquivo já processado
        if ($file->status === AccountFilesRepository::MOVEMENTS_DONE) {
            $this->handleReprocessing($file->accountId, $fileId, $file->referenceDate);
        } else {
            // Se for processamento de um novo arquivo, verificar se existem meses futuros já processados
            $this->handleNewProcessingWithFutureMonths($file->accountId, $file->referenceDate);
        }

        $status = $processingType == AccountFilesRepository::TYPE_PROCESSING_MOVEMENTS_EXTRACTION
            ? AccountFilesRepository::MOVEMENTS_IN_PROGRESS
            : AccountFilesRepository::CONCILIATION_IN_PROGRESS;

        $statusChanged = $this->changeFileProcessingStatusAction->execute($fileId, $status);

        if ($statusChanged) {
            ProcessAccountFileJob::dispatchSync($fileId, $processingType, $tenant);
        }

        return null;
    }

    /**
     * Check if account has valid initial balance for the file reference date
     *
     * Only requires initial balance when processing a month BEFORE the earliest processed month.
     * Sequential processing (same month or later) uses the balance from previous processed months.
     *
     * @param  string  $fileReferenceDate  Format: Y-m
     * @return array|null Returns array if initial balance is needed, null if OK
     */
    private function checkInitialBalance(
        int $accountId,
        string $fileReferenceDate,
        ?float $initialBalance,
        ?string $initialBalanceDate
    ): ?array {
        // Se já foi informado o saldo inicial, não precisa verificar
        if ($initialBalance !== null && $initialBalanceDate !== null) {
            return null;
        }

        $account = $this->accountRepository->getAccountsById($accountId);

        // Buscar o menor mês já processado para essa conta
        $earliestProcessedFile = $this->accountFilesRepository->getEarliestProcessedFile($accountId);

        // Se não existe nenhum arquivo processado, verificar o saldo inicial da conta
        if ($earliestProcessedFile === null) {
            $requiredBalanceMonth = Carbon::createFromFormat('Y-m', $fileReferenceDate)
                ->subMonth()
                ->format('Y-m');

            // Verificar se o saldo inicial da conta corresponde ao mês anterior ao extrato
            if ($account->initialBalanceDate !== $requiredBalanceMonth) {
                return $this->buildInitialBalanceResponse($account, $fileReferenceDate, $requiredBalanceMonth);
            }

            return null;
        }

        // Se o mês sendo processado é anterior ao menor mês já processado,
        // precisa informar o saldo do mês anterior ao extrato
        $earliestProcessedMonth = $earliestProcessedFile->referenceDate;

        if ($fileReferenceDate < $earliestProcessedMonth) {
            $requiredBalanceMonth = Carbon::createFromFormat('Y-m', $fileReferenceDate)
                ->subMonth()
                ->format('Y-m');

            // Verificar se o saldo inicial da conta corresponde ao mês anterior ao extrato
            if ($account->initialBalanceDate !== $requiredBalanceMonth) {
                return $this->buildInitialBalanceResponse($account, $fileReferenceDate, $requiredBalanceMonth);
            }
        }

        // Mês posterior ou igual ao menor já processado - não precisa de saldo inicial
        return null;
    }

    /**
     * Build the initial balance required response array
     */
    private function buildInitialBalanceResponse($account, string $fileReferenceDate, string $requiredBalanceMonth): array
    {
        return [
            'requiresInitialBalance' => true,
            'requiredBalanceMonth' => $requiredBalanceMonth,
            'requiredBalanceMonthFormatted' => $this->formatSingleMonth($requiredBalanceMonth),
            'currentMonth' => $this->formatSingleMonth($fileReferenceDate),
            'currentBalanceMonth' => $account->initialBalanceDate,
            'currentBalanceMonthFormatted' => $account->initialBalanceDate ? $this->formatSingleMonth($account->initialBalanceDate) : null,
            'currentBalance' => $account->initialBalance,
        ];
    }

    /**
     * Format future months for display (e.g., "08/2025, 09/2025")
     */
    private function formatMonthsForDisplay($futureFiles): string
    {
        return $futureFiles->map(function ($file) {
            return $this->formatSingleMonth($file->referenceDate);
        })->implode(', ');
    }

    /**
     * Format a single month for display (Y-m to MM/YYYY)
     */
    private function formatSingleMonth(string $referenceDate): string
    {
        $parts = explode('-', $referenceDate);

        return $parts[1].'/'.$parts[0];
    }

    /**
     * Handle reprocessing of an already processed file
     *
     * @param  string  $referenceDate  Format: Y-m
     */
    private function handleReprocessing(int $accountId, int $fileId, string $referenceDate): void
    {
        // Verificar se existem meses posteriores processados
        $futureFiles = $this->getFutureProcessedFilesAction->execute($accountId, $referenceDate);

        if ($futureFiles->isNotEmpty()) {
            // Apagar dados dos meses futuros
            $this->deleteFutureProcessedDataAction->execute($accountId, $referenceDate);
        }

        // Apagar dados do mês atual sendo reprocessado
        $this->deleteAccountMovementsAction->execute($accountId, $fileId);
        $this->deleteAnonymousEntriesAndExitsAction->execute($accountId, $referenceDate);
    }

    /**
     * Handle new file processing when future months already exist
     *
     * This happens when user is processing an older month (e.g., July)
     * but September and October are already processed.
     *
     * @param  string  $referenceDate  Format: Y-m
     */
    private function handleNewProcessingWithFutureMonths(int $accountId, string $referenceDate): void
    {
        // Verificar se existem meses posteriores já processados
        $futureFiles = $this->getFutureProcessedFilesAction->execute($accountId, $referenceDate);

        if ($futureFiles->isNotEmpty()) {
            // Apagar dados dos meses futuros
            $this->deleteFutureProcessedDataAction->execute($accountId, $referenceDate);
        }
    }
}
