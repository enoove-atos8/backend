<?php

namespace App\Domain\Financial\Reports\Exits\Actions;

use App\Application\Core\Jobs\Financial\Reports\Exits\HandlerExitsReports;
use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Files\ExistsFileByReferenceDateAction;
use App\Domain\Financial\Reports\Exits\Constants\ReturnMessages;
use App\Domain\Financial\Reports\Exits\DataTransferObjects\MonthlyExitsReportData;
use App\Domain\Financial\Reports\Exits\Interfaces\MonthlyExitsReportsRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountFilesRepository;

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
                $file = $this->existsFileByReferenceDateAction->execute($account->id, $monthlyExitsReportData->dates[0]);

                if($file)
                {
                    if ($file->status === AccountFilesRepository::MOVEMENTS_DONE || $file->status === AccountFilesRepository::CONCILIATION_DONE) {
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
                    } else {
                        throw new GeneralExceptions(ReturnMessages::EXTRACT_NOT_PROCESSED, 400);
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
