<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\ReportRequestsData;
use App\Domain\Financial\Entries\Reports\Interfaces\ReportRequestsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Reports\ReportRequestsRepository;

class GetReportsRequestsAction
{
    private ReportRequestsRepository $reportRequestsRepository;

    public function __construct(ReportRequestsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions
     * @throws BindingResolutionException
     */
    public function __invoke(): Collection
    {
        $reports = $this->reportRequestsRepository->getReports();

        if(count($reports))
            return $reports;
        else
            throw new GeneralExceptions(ReturnMessages::NO_REPORT_FOUNDED, 500);
    }
}
