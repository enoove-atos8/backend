<?php

namespace App\Domain\Financial\Reports\Exits\Actions;

use App\Application\Core\Jobs\Financial\Reports\Exits\HandlerExitsReports;
use App\Domain\Financial\Reports\Exits\Constants\ReturnMessages;
use App\Domain\Financial\Reports\Exits\DataTransferObjects\MonthlyExitsReportData;
use App\Domain\Financial\Reports\Exits\Interfaces\MonthlyExitsReportsRepositoryInterface;
use App\Domain\Financial\Reports\Exits\Models\ExitsReportRequests;
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
