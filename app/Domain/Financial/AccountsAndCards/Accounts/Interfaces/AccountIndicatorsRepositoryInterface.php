<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use Illuminate\Support\Collection;

interface AccountIndicatorsRepositoryInterface
{
    /**
     * Get accounts with current balance and last movement date
     */
    public function getAccountsIndicators(): Collection;

    /**
     * Get month summary (credits, debits, counts) grouped by account
     */
    public function getMonthSummary(string $referenceDate): Collection;

    /**
     * Get conciliation status by account for a given month
     */
    public function getConciliationStatus(string $referenceDate): Collection;

    /**
     * Get recent movements grouped by account (30 movements per account)
     */
    public function getRecentMovements(int $limit = 30): Collection;

    /**
     * Get pending files (files with status different from movements_done)
     */
    public function getPendingFiles(): Collection;
}
