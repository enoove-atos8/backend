<?php

namespace Domain\Financial\Exits\Reports\Actions;

use App\Domain\Financial\Exits\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Exits\Reports\DataTransferObjects\MonthlyExitsReportData;
use App\Domain\Financial\Exits\Reports\Interfaces\MonthlyExitsReportsRepositoryInterface;
use App\Domain\Financial\Exits\Reports\Models\ExitsReportRequests;
use Application\Core\Jobs\Financial\Exits\Reports\HandlerExitsReports;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateMonthlyExitsReceiptsReportAction
{
    private MonthlyExitsReportsRepositoryInterface $monthlyExitsReportsRepository;

    public function __construct(MonthlyExitsReportsRepositoryInterface $monthlyExitsReportsRepositoryInterface)
    {
        $this->monthlyExitsReportsRepository = $monthlyExitsReportsRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions
     */
    public function execute(MonthlyExitsReportData $monthlyExitsReceiptsReportData): ExitsReportRequests
    {
        $report = $this->monthlyExitsReportsRepository->generateMonthlyExitsReceiptsReport($monthlyExitsReceiptsReportData);

        if(!is_null($report->id))
        {
            HandlerExitsReports::dispatch();
            return $report;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
        }
    }
}
