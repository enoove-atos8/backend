<?php

namespace App\Domain\Financial\Reports\Entries\Actions;

use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;

class UpdateStatusReportRequestsAction
{
    private MonthlyReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyReportsRepositoryInterface $reportRequestsRepositoryInterface)
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
