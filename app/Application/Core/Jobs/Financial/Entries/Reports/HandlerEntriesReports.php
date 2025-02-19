<?php

namespace Application\Core\Jobs\Financial\Entries\Reports;

use App\Domain\CentralDomain\Plans\Actions\GetPlansAction;
use App\Infrastructure\Services\Atos8\Financial\Entries\Reports\GenerateMonthlyEntriesReport;
use App\Infrastructure\Services\Atos8\Financial\Entries\Reports\GenerateMonthlyReceiptsReport;
use App\Infrastructure\Services\Atos8\Financial\Entries\Reports\GenerateQuarterlyEntriesReports;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchByPlanIdAction;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesByPlanIdAction;
use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Domain\Financial\Entries\Reports\Actions\GetReportsRequestsAction;
use Domain\Financial\Entries\Reports\Actions\GetReportsRequestsByStatusAction;
use Domain\Financial\Entries\Reports\Actions\UpdateStatusReportRequestsAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\CentralDomain\PlanRepository;
use Infrastructure\Repositories\Financial\Entries\Reports\ReportRequestsRepository;
use Throwable;

class HandlerEntriesReports
{

    private GetReportsRequestsAction $getReportsRequestsAction;
    private GenerateMonthlyEntriesReport $generateMonthlyEntriesReport;
    private GenerateMonthlyReceiptsReport $generateMonthlyReceiptsReport;
    private GenerateQuarterlyEntriesReports $generateQuarterlyEntriesReports;
    private GetPlansAction $getPlansAction;
    private GetChurchesAction $getChurchesAction;
    private GetChurchesByPlanIdAction $getChurchesByPlanIdAction;
    private GetReportsRequestsByStatusAction $getReportsRequestsByStatusAction;
    private UpdateStatusReportRequestsAction $updateStatusReportRequestsAction;


    public function __construct(
        GetReportsRequestsAction $getReportsRequestsAction,
        GenerateMonthlyEntriesReport $generateMonthlyEntriesReport,
        GenerateMonthlyReceiptsReport $generateMonthlyReceiptsReport,
        GenerateQuarterlyEntriesReports $generateQuarterlyEntriesReports,
        GetPlansAction $getPlansAction,
        GetChurchesAction $getChurchesAction,
        GetChurchesByPlanIdAction $getChurchesByPlanIdAction,
        UpdateStatusReportRequestsAction $updateStatusReportRequestsAction,
        GetReportsRequestsByStatusAction $getReportsRequestsByStatusAction,
    )
    {
        $this->getReportsRequestsAction = $getReportsRequestsAction;
        $this->generateMonthlyEntriesReport = $generateMonthlyEntriesReport;
        $this->generateMonthlyReceiptsReport = $generateMonthlyReceiptsReport;
        $this->generateQuarterlyEntriesReports = $generateQuarterlyEntriesReports;
        $this->getPlansAction = $getPlansAction;
        $this->getChurchesAction = $getChurchesAction;
        $this->getChurchesByPlanIdAction = $getChurchesByPlanIdAction;
        $this->getReportsRequestsByStatusAction = $getReportsRequestsByStatusAction;
        $this->updateStatusReportRequestsAction = $updateStatusReportRequestsAction;
    }


    /**
     * @throws GeneralExceptions
     * @throws BindingResolutionException|Throwable
     */
    public function handle(): void
    {
        $tenants = $this->getActiveTenants();
        //$tenants = $this->getTenantsByPlan(PlanRepository::PLAN_GOLD_NAME);

        foreach ($tenants as $tenant)
        {
            tenancy()->initialize($tenant);

            $toProcessRequests = $this->getReportsRequestsByStatusAction->execute(ReportRequestsRepository::TO_PROCESS_STATUS_VALUE);

            if(count($toProcessRequests) > 0)
            {
                foreach ($toProcessRequests as $report)
                {
                    try
                    {
                        $this->updateStatusReportRequestsAction->execute($report->id, ReportRequestsRepository::IN_PROGRESS_STATUS_VALUE);

                        if($report->report_name == ReportRequestsRepository::MONTHLY_RECEIPTS_REPORT_NAME)
                            $this->generateMonthlyReceiptsReport->execute($report, $tenant);

                        //if($report->report_name == ReportRequestsRepository::MONTHLY_RECEIPTS_REPORT_NAME)
                        //    $this->generateMonthlyReceiptsReport->execute();

                        //if($report->report_name == ReportRequestsRepository::MONTHLY_ENTRIES_REPORT_NAME)
                        //    $this->generateQuarterlyEntriesReports->execute();
                    }
                    catch(GeneralExceptions $e)
                    {
                        if($e->getCode() == 404)
                            $this->updateStatusReportRequestsAction->execute($report->id, ReportRequestsRepository::NO_RECEIPTS_STATUS_VALUE);

                        if($e->getCode() == 500)
                            $this->updateStatusReportRequestsAction->execute($report->id, ReportRequestsRepository::ERROR_STATUS_VALUE);

                        throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
                    }
                }
            }
        }

    }



    /**
     * @throws Throwable
     */
    public function getActiveTenants(): array
    {
        $arrTenants = [];
        $tenants = $this->getChurchesAction->execute();

        if(count($tenants) > 0)
        {
            foreach ($tenants as $tenant)
                $arrTenants[] = $tenant->tenant_id;

            return $arrTenants;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::NOT_FOUND_CHURCHES, 404);
        }
    }
}
