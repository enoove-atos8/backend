<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\ReportRequestsData;
use App\Domain\Financial\Entries\Reports\Interfaces\ReportRequestsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Reports\ReportRequestsRepository;

class CreateReportRequestAction
{
    private ReportRequestsRepository $reportJobRepository;

    public function __construct(ReportRequestsRepositoryInterface $reportJobRepositoryInterface)
    {
        $this->reportJobRepository = $reportJobRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions
     */
    public function __invoke(ReportRequestsData $reportJobData): ReportRequests
    {
        $report = $this->reportJobRepository->generateReport($reportJobData);

        if(!is_null($report->id ))
            return $report;
        else
            throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
    }
}
