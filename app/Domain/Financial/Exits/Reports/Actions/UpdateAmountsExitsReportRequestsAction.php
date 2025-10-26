<?php

namespace Domain\Financial\Exits\Reports\Actions;

use App\Domain\Financial\Exits\Reports\Interfaces\MonthlyExitsReportsRepositoryInterface;

class UpdateAmountsExitsReportRequestsAction
{
    private MonthlyExitsReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyExitsReportsRepositoryInterface $reportRequestsRepositoryInterface)
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
        return $this->reportRequestsRepository->updateExitAmount($id, $amount);
    }
}
