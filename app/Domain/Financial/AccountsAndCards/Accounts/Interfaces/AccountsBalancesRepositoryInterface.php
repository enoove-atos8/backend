<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountBalanceData;

interface AccountsBalancesRepositoryInterface
{
    public function getBalanceByAccountAndDate(int $accountId, string $referenceDate): ?AccountBalanceData;

    public function saveOrUpdateBalance(AccountBalanceData $accountBalanceData): AccountBalanceData;

    /**
     * Delete balance by account and reference date
     *
     * @param  string  $referenceDate  Format: Y-m
     */
    public function deleteByAccountAndReferenceDate(int $accountId, string $referenceDate): bool;
}
