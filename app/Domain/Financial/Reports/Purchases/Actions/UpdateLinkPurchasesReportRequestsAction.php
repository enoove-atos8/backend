<?php

namespace App\Domain\Financial\Reports\Purchases\Actions;

use App\Domain\Financial\Reports\Purchases\Interfaces\MonthlyPurchasesReportsRepositoryInterface;

class UpdateLinkPurchasesReportRequestsAction
{
    private MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     */
    public function execute($id, string $link): bool
    {
        return $this->reportRequestsRepository->updateLinkReport($id, $link);
    }
}
