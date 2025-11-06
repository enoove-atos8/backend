<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountBalanceData;

interface AccountsBalancesRepositoryInterface
{
    public function getBalanceByAccountAndDate(int $accountId, string $referenceDate): ?AccountBalanceData;

    public function saveOrUpdateBalance(AccountBalanceData $accountBalanceData): AccountBalanceData;
}
