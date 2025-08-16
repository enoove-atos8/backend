<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions;

use Domain\Financial\AccountsAndCards\Accounts\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeactivateAccountAction
{
    protected AccountRepositoryInterface $accountRepository;

    /**
     * Create a new SaveCardAction instance.
     *
     * @param AccountRepositoryInterface $accountRepository
     */
    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Execute the action to deactivate an account
     *
     * @param int $accountId
     * @return mixed
     */
    public function execute(int $accountId): mixed
    {
        return $this->accountRepository->deactivateAccount($accountId);
    }
}
