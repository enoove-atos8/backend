<?php

namespace Domain\Financial\Exits\Reports\Actions;

use App\Domain\Financial\Exits\Reports\Interfaces\MonthlyExitsReportsRepositoryInterface;
use Infrastructure\Repositories\Financial\Exits\Reports\MonthlyExitsReportsRepository;

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
