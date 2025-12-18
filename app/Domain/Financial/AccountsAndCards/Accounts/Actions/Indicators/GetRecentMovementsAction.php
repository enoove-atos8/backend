<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Indicators;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountIndicatorsRepositoryInterface;
use Illuminate\Support\Collection;

class GetRecentMovementsAction
{
    public function __construct(
        private AccountIndicatorsRepositoryInterface $repository
    ) {}

    public function execute(int $limit = 30): Collection
    {
        return $this->repository->getRecentMovements($limit);
    }
}
