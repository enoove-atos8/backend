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
     * Save a new account or update an existing one.
     *
     * @param int $accountId
     * @return mixed
     */
    public function deactivateAccount(int $accountId): mixed;


    /**
     * Get accounts
     *
     * @return Collection
     */
    public function getAccounts(): Collection;


    /**
     * Get account by id
     *
     * @param int $id
     * @return AccountData|null
     */
    public function getAccountsById(int $id): ?AccountData;
}
