<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountRepositoryInterface;
use Illuminate\Support\Collection;

class GetAccountsAction
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
     * Execute the action to save a card.
     *
     * @param bool $returnDeactivatesAccounts
     * @return Collection
     */
    public function execute(bool $returnDeactivatesAccounts = false): Collection
    {
        return $this->accountRepository->getAccounts($returnDeactivatesAccounts);
    }
}
