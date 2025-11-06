<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;

class ExistsFileByReferenceDateAction
{
    private AccountFileRepositoryInterface $accountFileRepository;

    public function __construct(AccountFileRepositoryInterface $accountFileRepository)
    {
        $this->accountFileRepository = $accountFileRepository;
    }

    public function execute(int $accountId, string $referenceDate): ?AccountFileData
    {
        return $this->accountFileRepository->getFileByAccountAndReferenceDate($accountId, $referenceDate);
    }
}
