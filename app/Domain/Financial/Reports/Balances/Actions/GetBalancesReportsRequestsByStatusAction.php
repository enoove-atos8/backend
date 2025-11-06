<?php

namespace App\Domain\Financial\Reports\Balances\Actions;

use App\Domain\Financial\Reports\Balances\Interfaces\MonthlyBalancesReportsRepositoryInterface;
use Illuminate\Support\Collection;

class GetBalancesReportsRequestsByStatusAction
{
    private MonthlyBalancesReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyBalancesReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }

    public function execute(string $status): Collection
    {
        return $this->reportRequestsRepository->getReportsByStatus($status);
    }
}
