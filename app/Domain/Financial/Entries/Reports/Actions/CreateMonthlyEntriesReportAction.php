<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Files\ExistsFileByReferenceDateAction;
use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Entries\Reports\Interfaces\MonthlyReportsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Application\Core\Jobs\Financial\Entries\Reports\HandlerEntriesReports;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateMonthlyEntriesReportAction
{
    private MonthlyReportsRepositoryInterface $reportsRepository;
    private GetAccountsAction $getAccountsAction;
    private ExistsFileByReferenceDateAction $existsFileByReferenceDateAction;

    public function __construct(
        MonthlyReportsRepositoryInterface $monthlyReportsRepositoryInterface,
        GetAccountsAction $getAccountsAction,
        ExistsFileByReferenceDateAction $existsFileByReferenceDateAction
    )
    {
        $this->reportsRepository = $monthlyReportsRepositoryInterface;
        $this->getAccountsAction = $getAccountsAction;
        $this->existsFileByReferenceDateAction = $existsFileByReferenceDateAction;
    }


    /**
     * @throws GeneralExceptions
     */
    public function execute(MonthlyReportData $monthlyReportData): void
    {
        $accountsByTenant = $this->getAccountsAction->execute();

        if(count($accountsByTenant) > 0)
        {
            foreach ($accountsByTenant as $account)
            {
                $existExtractBankToReferenceDate = $this->existsFileByReferenceDateAction->execute($account->id, $monthlyReportData->dates[0]);

                if($existExtractBankToReferenceDate)
                {
                    $monthlyReportData->accountId = $account->id;
                    $report = $this->reportsRepository->generateMonthlyEntriesReport($monthlyReportData);

                    if(!is_null($report->id ))
                        HandlerEntriesReports::dispatch();

                    else
                        throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
                }
                else
                {
                    throw new GeneralExceptions(ReturnMessages::EXTRACT_NOT_FOUND, 404);
                }
            }
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_ACCOUNTS_NOT_FOUND_AND_EXTRACT_NOT_FOUND, 404);
        }
    }
}
