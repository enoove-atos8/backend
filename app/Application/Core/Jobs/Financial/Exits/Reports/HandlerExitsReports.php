<?php

namespace Application\Core\Jobs\Financial\Exits\Reports;

use App\Domain\CentralDomain\Plans\Actions\GetPlansAction;
use App\Infrastructure\Services\Atos8\Financial\Exits\Reports\GenerateMonthlyExitsReport;
use App\Infrastructure\Services\Atos8\Financial\Exits\Reports\GenerateMonthlyReceiptsReport;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesByPlanIdAction;
use Domain\Financial\Exits\Reports\Actions\GetExitsReportsRequestsAction;
use Domain\Financial\Exits\Reports\Actions\GetExitsReportsRequestsByStatusAction;
use Domain\Financial\Exits\Reports\Actions\UpdateStatusExitsReportRequestsAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Reports\MonthlyExitsReportsRepository;
use Throwable;

class HandlerExitsReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private GetExitsReportsRequestsAction $getExitsReportsRequestsAction;
    private GenerateMonthlyExitsReport $generateMonthlyExitsReport;
    private GenerateMonthlyReceiptsReport $generateMonthlyReceiptsReport;
    private GetPlansAction $getPlansAction;
    private GetChurchesAction $getChurchesAction;
    private GetChurchesByPlanIdAction $getChurchesByPlanIdAction;
    private GetExitsReportsRequestsByStatusAction $getExitsReportsRequestsByStatusAction;
    private UpdateStatusExitsReportRequestsAction $updateStatusExitsReportRequestsAction;


    public function __construct()
    {
    }


    /**
     * @throws GeneralExceptions
     * @throws BindingResolutionException|Throwable
     */
    public function handle(
        GetExitsReportsRequestsAction $getExitsReportsRequestsAction,
        GenerateMonthlyExitsReport $generateMonthlyExitsReport,
        GenerateMonthlyReceiptsReport $generateMonthlyReceiptsReport,
        GetPlansAction $getPlansAction,
        GetChurchesAction $getChurchesAction,
        GetChurchesByPlanIdAction $getChurchesByPlanIdAction,
        UpdateStatusExitsReportRequestsAction $updateStatusExitsReportRequestsAction,
        GetExitsReportsRequestsByStatusAction $getExitsReportsRequestsByStatusAction,
    ): void
    {
        $tenants = $this->getActiveTenants($getChurchesAction);

        foreach ($tenants as $tenant)
        {
            tenancy()->initialize($tenant);

            $reports = $getExitsReportsRequestsByStatusAction->execute(MonthlyExitsReportsRepository::TO_PROCESS_STATUS_VALUE);

            if(count($reports) > 0)
            {
                foreach ($reports as $report)
                {
                    try
                    {
                        $updateStatusExitsReportRequestsAction->execute($report->id, MonthlyExitsReportsRepository::IN_PROGRESS_STATUS_VALUE);

                        if($report->reportName == MonthlyExitsReportsRepository::MONTHLY_EXITS_REPORT_NAME)
                        {
                            $generateMonthlyExitsReport->execute($report, $tenant);
                        }

                        if($report->reportName == MonthlyExitsReportsRepository::MONTHLY_RECEIPTS_REPORT_NAME)
                        {
                            $generateMonthlyReceiptsReport->execute($report, $tenant);
                        }
                    }
                    catch(GeneralExceptions $e)
                    {
                        if($e->getCode() == 404)
                        {
                            $updateStatusExitsReportRequestsAction->execute($report->id, MonthlyExitsReportsRepository::NO_DATA_STATUS_VALUE);
                        }

                        if($e->getCode() == 500)
                        {
                            $updateStatusExitsReportRequestsAction->execute($report->id, MonthlyExitsReportsRepository::ERROR_STATUS_VALUE);
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

        if(count($tenants) > 0)
        {
            foreach ($tenants as $tenant)
            {
                $arrTenants[] = $tenant->tenant_id;
            }
        }

        return $arrTenants;
    }
}
