<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;

class GetLastProcessedFileAction
{
    private AccountFileRepositoryInterface $accountFileRepository;

    public function __construct(AccountFileRepositoryInterface $accountFileRepository)
    {
        $this->accountFileRepository = $accountFileRepository;
    }

    /**
     * Get the last processed file for an account
     */
    public function execute(int $accountId): ?AccountFileData
    {
        return $this->accountFileRepository->getLastProcessedFile($accountId);
    }
}
