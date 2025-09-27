<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\Entries\Reports\Interfaces\MonthlyReportsRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GetReportsRequestsAction
{
    private MonthlyReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyReportsRepositoryInterface $reportRequestsRepositoryInterface)
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
