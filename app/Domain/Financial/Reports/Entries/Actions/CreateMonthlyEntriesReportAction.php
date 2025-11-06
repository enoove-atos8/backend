<?php

namespace App\Domain\Financial\Reports\Entries\Actions;

use App\Application\Core\Jobs\Financial\Reports\Entries\HandlerEntriesReports;
use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Files\ExistsFileByReferenceDateAction;
use App\Domain\Financial\Reports\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Reports\Entries\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountFilesRepository;

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
                $file = $this->existsFileByReferenceDateAction->execute($account->id, $monthlyReportData->dates[0]);

                if($file)
                {
                    if ($file->status === AccountFilesRepository::MOVEMENTS_DONE || $file->status === AccountFilesRepository::CONCILIATION_DONE) {
                        $monthlyReportData->accountId = $account->id;
                        $report = $this->reportsRepository->generateMonthlyEntriesReport($monthlyReportData);

                        if(!is_null($report->id ))
                            HandlerEntriesReports::dispatch();

                        else
                            throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
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
