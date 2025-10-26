<?php

namespace Domain\Financial\Exits\Reports\Actions;

use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Files\ExistsFileByReferenceDateAction;
use App\Domain\Financial\Exits\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Exits\Reports\DataTransferObjects\MonthlyExitsReportData;
use App\Domain\Financial\Exits\Reports\Interfaces\MonthlyExitsReportsRepositoryInterface;
use Application\Core\Jobs\Financial\Exits\Reports\HandlerExitsReports;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateMonthlyExitsReportAction
{
    private MonthlyExitsReportsRepositoryInterface $reportsRepository;
    private GetAccountsAction $getAccountsAction;
    private ExistsFileByReferenceDateAction $existsFileByReferenceDateAction;

    public function __construct(
        MonthlyExitsReportsRepositoryInterface $monthlyExitsReportsRepositoryInterface,
        GetAccountsAction $getAccountsAction,
        ExistsFileByReferenceDateAction $existsFileByReferenceDateAction
    )
    {
        $this->reportsRepository = $monthlyExitsReportsRepositoryInterface;
        $this->getAccountsAction = $getAccountsAction;
        $this->existsFileByReferenceDateAction = $existsFileByReferenceDateAction;
    }


    /**
     * @throws GeneralExceptions
     */
    public function execute(MonthlyExitsReportData $monthlyExitsReportData): void
    {
        $accountsByTenant = $this->getAccountsAction->execute();

        if(count($accountsByTenant) > 0)
        {
            foreach ($accountsByTenant as $account)
            {
                $existExtractBankToReferenceDate = $this->existsFileByReferenceDateAction->execute($account->id, $monthlyExitsReportData->dates[0]);

                if($existExtractBankToReferenceDate)
                {
                    $monthlyExitsReportData->accountId = $account->id;
                    $report = $this->reportsRepository->generateMonthlyExitsReport($monthlyExitsReportData);

                    if(!is_null($report->id))
                    {
                        HandlerExitsReports::dispatch();
                    }
                    else
                    {
                        throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
                    }
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
