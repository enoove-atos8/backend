<?php

namespace App\Domain\Financial\Reports\Purchases\Actions;

use App\Domain\Financial\Reports\Purchases\Interfaces\MonthlyPurchasesReportsRepositoryInterface;
use Illuminate\Support\Collection;

class GetPurchasesReportsRequestsByStatusAction
{
    private MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepository;

    public function __construct(MonthlyPurchasesReportsRepositoryInterface $reportRequestsRepositoryInterface)
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
