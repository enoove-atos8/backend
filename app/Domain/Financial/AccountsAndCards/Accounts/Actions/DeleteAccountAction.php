<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions;

use Domain\Financial\AccountsAndCards\Accounts\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteAccountAction
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository
    ) {}

    /**
     * Execute the action to permanently delete an account.
     *
     * @throws GeneralExceptions
     */
    public function execute(int $accountId): bool
    {
        $deleted = $this->accountRepository->deleteAccount($accountId);

        if ($deleted) {
            return true;
        }

        throw new GeneralExceptions(ReturnMessages::ACCOUNT_NOT_DELETED, 500);
    }
}
