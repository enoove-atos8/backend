<?php

namespace Domain\Financial\Exits\Reports\Actions;

use App\Domain\Financial\Exits\Reports\Interfaces\MonthlyExitsReportsRepositoryInterface;
use Illuminate\Support\Collection;

class GetExitsReportsRequestsByStatusAction
{
    private MonthlyExitsReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyExitsReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     * @return Collection
     */
    public function execute(string $status): Collection
    {
        return $this->reportRequestsRepository->getReportsByStatus($status);
    }
}
