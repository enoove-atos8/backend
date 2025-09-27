<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Entries\Reports\Interfaces\MonthlyReportsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Reports\MonthlyReportsRepository;

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
