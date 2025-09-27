<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Entries\Reports\Interfaces\MonthlyReportsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Reports\MonthlyReportsRepository;

class CreateMonthlyReceiptsReportAction
{
    private MonthlyReportsRepositoryInterface $monthlyReportsRepository;

    public function __construct(MonthlyReportsRepositoryInterface $monthlyReportsRepositoryInterface)
    {
        $this->monthlyReportsRepository = $monthlyReportsRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions
     */
    public function execute(MonthlyReportData $monthlyReceiptsReportData): ReportRequests
    {
        $report = $this->monthlyReportsRepository->generateMonthlyReceiptsReport($monthlyReceiptsReportData);

        if(!is_null($report->id))
            return $report;
        else
            throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
    }
}
