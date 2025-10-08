<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Entries\Reports\Interfaces\MonthlyReportsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateMonthlyEntriesReportAction
{
    private MonthlyReportsRepositoryInterface $reportsRepository;
    private GetAccountsAction $getAccountsAction;

    public function __construct(
        MonthlyReportsRepositoryInterface $monthlyReportsRepositoryInterface,
        GetAccountsAction $getAccountsAction
    )
    {
        $this->reportsRepository = $monthlyReportsRepositoryInterface;
        $this->getAccountsAction = $getAccountsAction;
    }


    /**
     * @throws GeneralExceptions
     */
    public function execute(MonthlyReportData $monthlyReportData): void
    {
        $accountsByTenant = $this->getAccountsAction->execute();

        foreach ($accountsByTenant as $account)
        {
            $monthlyReportData->accountId = $account->id;
            $report = $this->reportsRepository->generateMonthlyEntriesReport($monthlyReportData);

            if(!is_null($report->id ))
                continue;
            else
                throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
        }
    }
}
