<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Actions\Balances;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountBalanceData;
use App\Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountsBalancesRepositoryInterface;

class SaveOrUpdateBalanceAction
{
    private AccountsBalancesRepositoryInterface $accountsBalancesRepository;

    public function __construct(AccountsBalancesRepositoryInterface $accountsBalancesRepositoryInterface)
    {
        $this->accountsBalancesRepository = $accountsBalancesRepositoryInterface;
    }

    public function execute(AccountBalanceData $accountBalanceData): AccountBalanceData
    {
        return $this->accountsBalancesRepository->saveOrUpdateBalance($accountBalanceData);
    }
}
