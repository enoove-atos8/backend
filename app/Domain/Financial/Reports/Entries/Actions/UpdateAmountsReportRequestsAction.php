<?php

namespace App\Domain\Financial\Reports\Entries\Actions;

use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Reports\Entries\MonthlyReportsRepository;

class UpdateAmountsReportRequestsAction
{
    private MonthlyReportsRepository $reportRequestsRepository;

    public function __construct(MonthlyReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     */
    public function execute($id, array $entryTypesAmount): bool
    {
        return $this->reportRequestsRepository->updateEntryTypesAmount($id, $entryTypesAmount);
    }
}
