<?php

namespace App\Domain\Financial\Reports\Balances\Actions;

use App\Application\Core\Jobs\Financial\Reports\Balances\HandlerBalancesReports;
use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Files\ExistsFileByReferenceDateAction;
use App\Domain\Financial\Reports\Balances\Constants\ReturnMessages;
use App\Domain\Financial\Reports\Balances\DataTransferObjects\MonthlyBalancesReportData;
use App\Domain\Financial\Reports\Balances\Interfaces\MonthlyBalancesReportsRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountFilesRepository;

class CreateMonthlyBalancesReportAction
{
    private MonthlyBalancesReportsRepositoryInterface $reportsRepository;

    private GetAccountsAction $getAccountsAction;

    private ExistsFileByReferenceDateAction $existsFileByReferenceDateAction;

    public function __construct(
        MonthlyBalancesReportsRepositoryInterface $monthlyBalancesReportsRepositoryInterface,
        GetAccountsAction $getAccountsAction,
        ExistsFileByReferenceDateAction $existsFileByReferenceDateAction
    ) {
        $this->reportsRepository = $monthlyBalancesReportsRepositoryInterface;
        $this->getAccountsAction = $getAccountsAction;
        $this->existsFileByReferenceDateAction = $existsFileByReferenceDateAction;
    }

    /**
     * @throws GeneralExceptions
     */
    public function execute(MonthlyBalancesReportData $monthlyBalancesReportData): void
    {
        $accountsByTenant = $this->getAccountsAction->execute();

        if (count($accountsByTenant) > 0) {
            foreach ($accountsByTenant as $account) {
                $file = $this->existsFileByReferenceDateAction->execute($account->id, $monthlyBalancesReportData->dates[0]);

                if ($file) {
                    if ($file->status === AccountFilesRepository::MOVEMENTS_DONE || $file->status === AccountFilesRepository::CONCILIATION_DONE) {
                        $monthlyBalancesReportData->accountId = $account->id;
                        $report = $this->reportsRepository->generateMonthlyBalancesReport($monthlyBalancesReportData);

                        if (! is_null($report->id)) {
                            HandlerBalancesReports::dispatch();
                        } else {
                            throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
                        }
                    } else {
                        throw new GeneralExceptions(ReturnMessages::EXTRACT_NOT_PROCESSED, 400);
                    }
                } else {
                    throw new GeneralExceptions(ReturnMessages::EXTRACT_NOT_FOUND, 404);
                }
            }
        } else {
            throw new GeneralExceptions(ReturnMessages::ERROR_ACCOUNTS_NOT_FOUND_AND_EXTRACT_NOT_FOUND, 404);
        }
    }
}
