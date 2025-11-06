<?php

namespace App\Domain\Financial\Reports\Exits\Actions;

use App\Domain\Financial\Reports\Exits\Interfaces\MonthlyExitsReportsRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Reports\Exits\MonthlyExitsReportsRepository;

class UpdateLinkExitsReportRequestsAction
{
    private MonthlyExitsReportsRepository $reportRequestsRepository;

    public function __construct(MonthlyExitsReportsRepositoryInterface $reportRequestsRepositoryInterface)
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
