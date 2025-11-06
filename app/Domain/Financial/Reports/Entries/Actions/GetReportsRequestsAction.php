<?php

namespace App\Domain\Financial\Reports\Entries\Actions;

use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;
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
