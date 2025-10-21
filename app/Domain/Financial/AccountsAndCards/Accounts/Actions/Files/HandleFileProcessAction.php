<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use Application\Core\Jobs\Financial\Accounts\ProcessAccountFileJob;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Movements\DeleteAccountMovementsAction;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountFilesRepository;

class HandleFileProcessAction
{
    private ChangeFileProcessingStatusAction $changeFileProcessingStatusAction;
    private AccountFileRepositoryInterface $accountFilesRepository;
    private DeleteAccountMovementsAction $deleteAccountMovementsAction;

    public function __construct(
        ChangeFileProcessingStatusAction $changeFileProcessingStatusAction,
        AccountFileRepositoryInterface $accountFilesRepository,
        DeleteAccountMovementsAction $deleteAccountMovementsAction
    )
    {
        $this->changeFileProcessingStatusAction = $changeFileProcessingStatusAction;
        $this->accountFilesRepository = $accountFilesRepository;
        $this->deleteAccountMovementsAction = $deleteAccountMovementsAction;
    }


    /**
     * @param int $fileId
     * @param string $processingType
     * @param string $tenant
     */
    public function execute(int $fileId, string $processingType, string $tenant): void
    {
        $file = $this->accountFilesRepository->getFilesById($fileId);

        if ($file->status === AccountFilesRepository::MOVEMENTS_DONE) {
            $this->deleteAccountMovementsAction->execute($file->accountId, $fileId);
        }

        $status = $processingType == AccountFilesRepository::TYPE_PROCESSING_MOVEMENTS_EXTRACTION
            ? AccountFilesRepository::MOVEMENTS_IN_PROGRESS
            : AccountFilesRepository::CONCILIATION_IN_PROGRESS;

        $statusChanged = $this->changeFileProcessingStatusAction->execute($fileId, $status);

        if($statusChanged)
        {
            ProcessAccountFileJob::dispatch($fileId, $processingType, $tenant);
        }
    }
}
