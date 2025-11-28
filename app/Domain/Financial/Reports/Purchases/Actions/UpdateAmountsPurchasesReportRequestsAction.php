<?php

namespace App\Domain\Financial\Reports\Purchases\Actions;

use App\Domain\Financial\Reports\Purchases\Interfaces\MonthlyPurchasesReportsRepositoryInterface;

class UpdateAmountsPurchasesReportRequestsAction
{
    private MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     * @param int $id
     * @param float $amount
     * @return bool
     */
    public function execute(int $id, float $amount): bool
    {
        return $this->reportRequestsRepository->updatePurchaseAmount($id, $amount);
    }
}
