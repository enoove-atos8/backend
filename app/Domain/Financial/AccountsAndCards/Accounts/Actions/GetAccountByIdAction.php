<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountRepositoryInterface;
use Illuminate\Support\Collection;

class GetAccountByIdAction
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
     * @param int $id
     * @return AccountData
     */
    public function execute(int $id): AccountData
    {
        return $this->accountRepository->getAccountsById($id);
    }
}
