<?php

namespace App\Domain\Financial\Reports\Entries\Actions;

use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Reports\Entries\MonthlyReportsRepository;

class UpdateLinkReportRequestsAction
{
    private MonthlyReportsRepository $reportRequestsRepository;

    public function __construct(MonthlyReportsRepositoryInterface $reportRequestsRepositoryInterface)
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
