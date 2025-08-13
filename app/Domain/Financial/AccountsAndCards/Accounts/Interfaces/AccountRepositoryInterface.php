<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Illuminate\Support\Collection;

interface AccountRepositoryInterface
{
    /**
     * Save a new account or update an existing one.
     *
     * @param AccountData $accountData
     * @return AccountData
     */
    public function saveAccount(AccountData $accountData): AccountData;


    /**
     * Get accounts
     *
     * @return Collection
     */
    public function getAccounts(): Collection;
}
