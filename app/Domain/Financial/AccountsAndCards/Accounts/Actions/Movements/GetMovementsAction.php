<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\MovementsRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GetMovementsAction
{
    public function __construct(
        private readonly MovementsRepositoryInterface $movementsRepository
    ) {}

    /**
     * Execute the action to get movements
     *
     * @param int $accountId
     * @param string $referenceDate
     * @param bool $paginate
     * @return Collection|Paginator
     */
    public function execute(int $accountId, string $referenceDate, bool $paginate = true): Collection | Paginator
    {
        return $this->movementsRepository->getMovements($accountId, $referenceDate, $paginate);
    }
}
