<?php

namespace App\Domain\Financial\Reports\Exits\Actions;

use App\Domain\Financial\Reports\Exits\Interfaces\MonthlyExitsReportsRepositoryInterface;

class UpdateStatusExitsReportRequestsAction
{
    private MonthlyExitsReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyExitsReportsRepositoryInterface $reportRequestsRepositoryInterface)
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
