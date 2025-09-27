<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Entries\Reports\Interfaces\MonthlyReportsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateMonthlyEntriesReportAction
{
    private MonthlyReportsRepositoryInterface $reportsRepository;

    public function __construct(MonthlyReportsRepositoryInterface $monthlyReportsRepositoryInterface)
    {
        $this->reportsRepository = $monthlyReportsRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions
     */
    public function execute(MonthlyReportData $monthlyReportData): ReportRequests
    {
        $report = $this->reportsRepository->generateMonthlyReceiptsReport($monthlyReportData);

        if(!is_null($report->id ))
            return $report;
        else
            throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
    }
}
