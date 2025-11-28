<?php

namespace App\Domain\Financial\Reports\Purchases\Actions;

use App\Domain\Financial\Reports\Purchases\Interfaces\MonthlyPurchasesReportsRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GetPurchasesReportsRequestsAction
{
    private MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepositoryInterface)
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
