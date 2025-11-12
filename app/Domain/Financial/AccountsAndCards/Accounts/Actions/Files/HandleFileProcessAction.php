<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use App\Domain\Financial\AccountsAndCards\Accounts\Constants\Files\ReturnMessages;
use Application\Core\Jobs\Financial\Accounts\ProcessAccountFileJob;
use Carbon\Carbon;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Movements\DeleteAccountMovementsAction;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountFilesRepository;

class HandleFileProcessAction
{
    private ChangeFileProcessingStatusAction $changeFileProcessingStatusAction;

    private AccountFileRepositoryInterface $accountFilesRepository;

    private DeleteAccountMovementsAction $deleteAccountMovementsAction;

    private DeleteAnonymousEntriesAndExitsAction $deleteAnonymousEntriesAndExitsAction;

    private GetLastProcessedFileAction $getLastProcessedFileAction;

    public function __construct(
        ChangeFileProcessingStatusAction $changeFileProcessingStatusAction,
        AccountFileRepositoryInterface $accountFilesRepository,
        DeleteAccountMovementsAction $deleteAccountMovementsAction,
        DeleteAnonymousEntriesAndExitsAction $deleteAnonymousEntriesAndExitsAction,
        GetLastProcessedFileAction $getLastProcessedFileAction
    ) {
        $this->changeFileProcessingStatusAction = $changeFileProcessingStatusAction;
        $this->accountFilesRepository = $accountFilesRepository;
        $this->deleteAccountMovementsAction = $deleteAccountMovementsAction;
        $this->deleteAnonymousEntriesAndExitsAction = $deleteAnonymousEntriesAndExitsAction;
        $this->getLastProcessedFileAction = $getLastProcessedFileAction;
    }

    /**
     * @throws GeneralExceptions
     */
    public function execute(int $fileId, string $processingType, string $tenant): void
    {
        $file = $this->accountFilesRepository->getFilesById($fileId);

        // Se for reprocessamento, validar se é o último mês processado
        if ($file->status === AccountFilesRepository::MOVEMENTS_DONE) {
            $this->validateReprocessingIsLastMonth($file->accountId, $file->referenceDate);
            $this->deleteAccountMovementsAction->execute($file->accountId, $fileId);
            $this->deleteAnonymousEntriesAndExitsAction->execute($file->accountId, $file->referenceDate);
        }

        $status = $processingType == AccountFilesRepository::TYPE_PROCESSING_MOVEMENTS_EXTRACTION
            ? AccountFilesRepository::MOVEMENTS_IN_PROGRESS
            : AccountFilesRepository::CONCILIATION_IN_PROGRESS;

        $statusChanged = $this->changeFileProcessingStatusAction->execute($fileId, $status);

        if ($statusChanged) {
            ProcessAccountFileJob::dispatchSync($fileId, $processingType, $tenant);
        }
    }

    /**
     * Validate that reprocessing is only allowed for the last processed month
     * This prevents the need to recalculate subsequent months' balances
     *
     * Example:
     * - If months 2025-09 and 2025-10 are processed
     * - User can only reprocess 2025-10 (last month)
     * - Cannot reprocess 2025-09 (would require recalculating 2025-10)
     *
     * @param  string  $referenceDate  Format: Y-m
     *
     * @throws GeneralExceptions
     */
    private function validateReprocessingIsLastMonth(int $accountId, string $referenceDate): void
    {
        // Buscar o mês mais recente processado com sucesso para esta conta
        $lastProcessedFile = $this->getLastProcessedFileAction->execute($accountId);

        // Se não existe nenhum arquivo processado, permite o processamento
        if (! $lastProcessedFile) {
            return;
        }

        // Verificar se o arquivo sendo reprocessado é do último mês
        if ($referenceDate !== $lastProcessedFile->referenceDate) {
            $lastMonth = Carbon::createFromFormat('Y-m', $lastProcessedFile->referenceDate)->format('m/Y');
            $currentMonth = Carbon::createFromFormat('Y-m', $referenceDate)->format('m/Y');

            throw new GeneralExceptions(
                sprintf(ReturnMessages::REPROCESS_ONLY_LAST_MONTH, $currentMonth, $lastMonth),
                425
            );
        }
    }
}
