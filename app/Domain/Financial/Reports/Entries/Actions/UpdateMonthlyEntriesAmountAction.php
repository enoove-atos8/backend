<?php

namespace App\Domain\Financial\Reports\Entries\Actions;

use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;

class UpdateMonthlyEntriesAmountAction
{
    private MonthlyReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     * @param $id
     * @param string $amount
     * @return bool
     */
    public function execute($id, string $amount): bool
    {
        return $this->reportRequestsRepository->updateMonthlyEntriesAmount($id, $amount);
    }
}
