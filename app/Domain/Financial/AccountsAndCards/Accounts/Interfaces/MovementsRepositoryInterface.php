<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface MovementsRepositoryInterface
{
    /**
     * Get movements by account id and reference date
     *
     * @param int $accountId
     * @param string $referenceDate
     * @param bool $paginate
     * @return Collection|Paginator
     */
    public function getMovements(int $accountId, string $referenceDate, bool $paginate = true): Collection | Paginator;
}
