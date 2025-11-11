<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GetMovementsAction
{
    public function __construct(
        private readonly AccountMovementsRepositoryInterface $accountMovementsRepository
    ) {}

    /**
     * Execute the action to get movements
     */
    public function execute(int $accountId, string $referenceDate, bool $paginate = true): Collection|Paginator
    {
        return $this->accountMovementsRepository->getMovements($accountId, $referenceDate, $paginate);
    }
}
