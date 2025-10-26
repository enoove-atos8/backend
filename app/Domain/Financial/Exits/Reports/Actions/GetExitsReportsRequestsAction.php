<?php

namespace Domain\Financial\Exits\Reports\Actions;

use App\Domain\Financial\Exits\Reports\Interfaces\MonthlyExitsReportsRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GetExitsReportsRequestsAction
{
    private MonthlyExitsReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyExitsReportsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     * @return Collection|Paginator
     */
    public function execute(): Collection | Paginator
    {
        return $this->reportRequestsRepository->getReports();
    }
}
