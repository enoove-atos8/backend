<?php

namespace App\Domain\Financial\Reports\Entries\Actions;

use App\Application\Core\Jobs\Financial\Reports\Entries\HandlerEntriesReports;
use App\Domain\Financial\Reports\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Reports\Entries\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

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
