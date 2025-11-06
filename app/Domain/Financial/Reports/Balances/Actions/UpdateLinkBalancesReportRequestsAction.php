<?php

namespace App\Domain\Financial\Reports\Balances\Actions;

use App\Domain\Financial\Reports\Balances\Interfaces\MonthlyBalancesReportsRepositoryInterface;

class UpdateLinkBalancesReportRequestsAction
{
    private MonthlyBalancesReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyBalancesReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }

    public function execute($id, string $link): mixed
    {
        return $this->reportRequestsRepository->updateLinkReport($id, $link);
    }
}
