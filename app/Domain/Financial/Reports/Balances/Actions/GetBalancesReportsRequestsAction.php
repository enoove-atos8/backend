<?php

namespace App\Domain\Financial\Reports\Balances\Actions;

use App\Domain\Financial\Reports\Balances\Interfaces\MonthlyBalancesReportsRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GetBalancesReportsRequestsAction
{
    private MonthlyBalancesReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyBalancesReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }

    public function execute(): Collection|Paginator
    {
        return $this->reportRequestsRepository->getReports();
    }
}
