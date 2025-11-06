<?php

namespace App\Application\Core\Jobs\Financial\Reports\Balances;

use App\Domain\CentralDomain\Plans\Actions\GetPlansAction;
use App\Domain\Financial\Reports\Balances\Actions\GetBalancesReportsRequestsAction;
use App\Domain\Financial\Reports\Balances\Actions\GetBalancesReportsRequestsByStatusAction;
use App\Domain\Financial\Reports\Balances\Actions\UpdateStatusBalancesReportRequestsAction;
use App\Infrastructure\Repositories\Financial\Reports\Balances\MonthlyBalancesReportsRepository;
use App\Infrastructure\Services\Atos8\Financial\Balances\Reports\GenerateMonthlyBalancesReport;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesByPlanIdAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class HandlerBalancesReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private GetBalancesReportsRequestsAction $getBalancesReportsRequestsAction;

    private GenerateMonthlyBalancesReport $generateMonthlyBalancesReport;

    private GetPlansAction $getPlansAction;

    private GetChurchesAction $getChurchesAction;

    private GetChurchesByPlanIdAction $getChurchesByPlanIdAction;

    private GetBalancesReportsRequestsByStatusAction $getBalancesReportsRequestsByStatusAction;

    private UpdateStatusBalancesReportRequestsAction $updateStatusBalancesReportRequestsAction;

    public function __construct() {}

    /**
     * @throws GeneralExceptions
     * @throws BindingResolutionException|Throwable
     */
    public function handle(
        GetBalancesReportsRequestsAction $getBalancesReportsRequestsAction,
        GenerateMonthlyBalancesReport $generateMonthlyBalancesReport,
        GetPlansAction $getPlansAction,
        GetChurchesAction $getChurchesAction,
        GetChurchesByPlanIdAction $getChurchesByPlanIdAction,
        UpdateStatusBalancesReportRequestsAction $updateStatusBalancesReportRequestsAction,
        GetBalancesReportsRequestsByStatusAction $getBalancesReportsRequestsByStatusAction,
    ): void {
        $tenants = $this->getActiveTenants($getChurchesAction);

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            $reports = $getBalancesReportsRequestsByStatusAction->execute(MonthlyBalancesReportsRepository::TO_PROCESS_STATUS_VALUE);

            if (count($reports) > 0) {
                foreach ($reports as $report) {
                    try {
                        $updateStatusBalancesReportRequestsAction->execute($report->id, MonthlyBalancesReportsRepository::IN_PROGRESS_STATUS_VALUE);

                        if ($report->reportName == MonthlyBalancesReportsRepository::GENERAL_BALANCES_REPORT_NAME) {
                            $generateMonthlyBalancesReport->execute($report, $tenant);
                        }
                    } catch (GeneralExceptions $e) {
                        if ($e->getCode() == 404) {
                            $updateStatusBalancesReportRequestsAction->execute($report->id, MonthlyBalancesReportsRepository::NO_DATA_STATUS_VALUE);
                        }

                        if ($e->getCode() == 500) {
                            $updateStatusBalancesReportRequestsAction->execute($report->id, MonthlyBalancesReportsRepository::ERROR_STATUS_VALUE);
                        }

                        throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
                    }
                }
            }
        }

    }

    /**
     * @throws Throwable
     */
    public function getActiveTenants($getChurchesAction): array
    {
        $arrTenants = [];
        $tenants = $getChurchesAction->execute();

        if (count($tenants) > 0) {
            foreach ($tenants as $tenant) {
                $arrTenants[] = $tenant->tenant_id;
            }
        }

        return $arrTenants;
    }
}
