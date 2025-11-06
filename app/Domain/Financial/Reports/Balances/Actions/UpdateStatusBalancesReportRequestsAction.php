<?php

namespace App\Domain\Financial\Reports\Balances\Actions;

use App\Domain\Financial\Reports\Balances\Interfaces\MonthlyBalancesReportsRepositoryInterface;

class UpdateStatusBalancesReportRequestsAction
{
    private MonthlyBalancesReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyBalancesReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }

    public function execute($id, string $status): mixed
    {
        return $this->reportRequestsRepository->updateStatus($id, $status);
    }
}
