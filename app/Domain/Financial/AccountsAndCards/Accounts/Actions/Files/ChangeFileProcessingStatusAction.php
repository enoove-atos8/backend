<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Files;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;

class ChangeFileProcessingStatusAction
{
    private AccountFileRepositoryInterface $accountFileRepository;

    public function __construct(AccountFileRepositoryInterface $accountFileRepository)
    {
        $this->accountFileRepository = $accountFileRepository;
    }


    /**
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function execute(int $id, string $status): bool
    {
        return $this->accountFileRepository->changeFileProcessingStatus($id, $status);
    }
}
