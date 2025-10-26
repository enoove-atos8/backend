<?php

namespace Application\Core\Jobs\Financial\Entries\Reports;

use App\Domain\CentralDomain\Plans\Actions\GetPlansAction;
use App\Infrastructure\Services\Atos8\Financial\Entries\Reports\GenerateMonthlyEntriesReport;
use App\Infrastructure\Services\Atos8\Financial\Entries\Reports\GenerateMonthlyReceiptsReport;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesByPlanIdAction;
use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Domain\Financial\Entries\Reports\Actions\GetReportsRequestsAction;
use Domain\Financial\Entries\Reports\Actions\GetReportsRequestsByStatusAction;
use Domain\Financial\Entries\Reports\Actions\UpdateStatusReportRequestsAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\CentralDomain\PlanRepository;
use Infrastructure\Repositories\Financial\Entries\Reports\MonthlyReportsRepository;
use Throwable;

class HandlerEntriesReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private GetReportsRequestsAction $getReportsRequestsAction;
    private GenerateMonthlyEntriesReport $generateMonthlyEntriesReport;
    private GenerateMonthlyReceiptsReport $generateMonthlyReceiptsReport;
    private GetPlansAction $getPlansAction;
    private GetChurchesAction $getChurchesAction;
    private GetChurchesByPlanIdAction $getChurchesByPlanIdAction;
    private GetReportsRequestsByStatusAction $getReportsRequestsByStatusAction;
    private UpdateStatusReportRequestsAction $updateStatusReportRequestsAction;


    public function __construct()
    {
    }


    /**
     * @throws GeneralExceptions
     * @throws BindingResolutionException|Throwable
     */
    public function handle(
        GetReportsRequestsAction $getReportsRequestsAction,
        GenerateMonthlyEntriesReport $generateMonthlyEntriesReport,
        GenerateMonthlyReceiptsReport $generateMonthlyReceiptsReport,
        GetPlansAction $getPlansAction,
        GetChurchesAction $getChurchesAction,
        GetChurchesByPlanIdAction $getChurchesByPlanIdAction,
        UpdateStatusReportRequestsAction $updateStatusReportRequestsAction,
        GetReportsRequestsByStatusAction $getReportsRequestsByStatusAction,
    ): void
    {
        $tenants = $this->getActiveTenants($getChurchesAction);
        //$tenants = $this->getTenantsByPlan(PlanRepository::PLAN_GOLD_NAME);

        foreach ($tenants as $tenant)
        {
            tenancy()->initialize($tenant);

            $reports = $getReportsRequestsByStatusAction->execute(MonthlyReportsRepository::TO_PROCESS_STATUS_VALUE);

            if(count($reports) > 0)
            {
                foreach ($reports as $report)
                {
                    try
                    {
                        $updateStatusReportRequestsAction->execute($report->id, MonthlyReportsRepository::IN_PROGRESS_STATUS_VALUE);

                        if($report->reportName == MonthlyReportsRepository::MONTHLY_RECEIPTS_REPORT_NAME)
                            $generateMonthlyReceiptsReport->execute($report, $tenant);

                        if($report->reportName == MonthlyReportsRepository::MONTHLY_ENTRIES_REPORT_NAME)
                            $generateMonthlyEntriesReport->execute($report, $tenant);

                        //if($report->report_name == MonthlyReportsRepository::MONTHLY_ENTRIES_REPORT_NAME)
                        //    $generateQuarterlyEntriesReports->execute();
                    }
                    catch(GeneralExceptions $e)
                    {
                        if($e->getCode() == 404)
                            $updateStatusReportRequestsAction->execute($report->id, MonthlyReportsRepository::NO_RECEIPTS_STATUS_VALUE);

                        if($e->getCode() == 500)
                            $updateStatusReportRequestsAction->execute($report->id, MonthlyReportsRepository::ERROR_STATUS_VALUE);

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

        if(count($tenants) > 0)
        {
            foreach ($tenants as $tenant)
                $arrTenants[] = $tenant->tenant_id;
        }

        return $arrTenants;
    }
}
