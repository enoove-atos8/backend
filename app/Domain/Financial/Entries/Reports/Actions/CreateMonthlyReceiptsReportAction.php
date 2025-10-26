<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Entries\Reports\Interfaces\MonthlyReportsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Application\Core\Jobs\Financial\Entries\Reports\HandlerEntriesReports;
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
    public function execute(MonthlyReportData $monthlyReceiptsReportData): void
    {
        $report = $this->monthlyReportsRepository->generateMonthlyReceiptsReport($monthlyReceiptsReportData);

        if(!is_null($report->id))
        {
            HandlerEntriesReports::dispatch();
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
        }
    }
}
