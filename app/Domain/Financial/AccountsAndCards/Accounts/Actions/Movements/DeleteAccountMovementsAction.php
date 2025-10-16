<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;

class DeleteAccountMovementsAction
{
    private AccountMovementsRepositoryInterface $accountMovementsRepository;

    public function __construct(AccountMovementsRepositoryInterface $accountMovementsRepository)
    {
        $this->accountMovementsRepository = $accountMovementsRepository;
    }

    /**
     * Delete account movements by account and file
     *
     * @param int $accountId
     * @param int $fileId
     * @return bool
     */
    public function execute(int $accountId, int $fileId): bool
    {
        return $this->accountMovementsRepository->deleteByAccountAndFile($accountId, $fileId);
    }
}
