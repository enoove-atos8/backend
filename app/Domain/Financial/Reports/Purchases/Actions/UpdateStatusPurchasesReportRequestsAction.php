<?php

namespace App\Domain\Financial\Reports\Purchases\Actions;

use App\Domain\Financial\Reports\Purchases\Interfaces\MonthlyPurchasesReportsRepositoryInterface;

class UpdateStatusPurchasesReportRequestsAction
{
    private MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     */
    public function execute($id, string $status): bool
    {
        return $this->reportRequestsRepository->updateStatus($id, $status);
    }
}
