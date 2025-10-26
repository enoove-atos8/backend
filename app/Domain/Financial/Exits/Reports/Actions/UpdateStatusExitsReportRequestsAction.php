<?php

namespace Domain\Financial\Exits\Reports\Actions;

use App\Domain\Financial\Exits\Reports\Interfaces\MonthlyExitsReportsRepositoryInterface;

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
