<?php

namespace App\Domain\Financial\Reports\Entries\Actions;

use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;
use Illuminate\Support\Collection;

class GetReportsRequestsByStatusAction
{
    private MonthlyReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     * @param string $status
     * @return Collection
     */
    public function execute(string $status): Collection
    {
        return $this->reportRequestsRepository->getReportsByStatus($status);
    }
}
