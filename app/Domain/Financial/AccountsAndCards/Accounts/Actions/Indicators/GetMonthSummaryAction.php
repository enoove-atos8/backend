<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Indicators;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountIndicatorsRepositoryInterface;
use Illuminate\Support\Collection;

class GetMonthSummaryAction
{
    public function __construct(
        private AccountIndicatorsRepositoryInterface $repository
    ) {}

    public function execute(string $referenceDate): Collection
    {
        return $this->repository->getMonthSummary($referenceDate);
    }
}
