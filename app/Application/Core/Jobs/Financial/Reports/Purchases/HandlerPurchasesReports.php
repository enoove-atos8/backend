<?php

namespace App\Application\Core\Jobs\Financial\Reports\Purchases;

use App\Domain\CentralDomain\Plans\Actions\GetPlansAction;
use App\Domain\Financial\Reports\Purchases\Actions\GetPurchasesReportsRequestsAction;
use App\Domain\Financial\Reports\Purchases\Actions\GetPurchasesReportsRequestsByStatusAction;
use App\Domain\Financial\Reports\Purchases\Actions\UpdateStatusPurchasesReportRequestsAction;
use App\Infrastructure\Repositories\Financial\Reports\Purchases\MonthlyPurchasesReportsRepository;
use App\Infrastructure\Services\Atos8\Financial\Purchases\Reports\GenerateMonthlyPurchasesReport;
use App\Infrastructure\Services\Atos8\Financial\Purchases\Reports\GenerateMonthlyReceiptsPurchaseReport;
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

class HandlerPurchasesReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * O número de segundos que o job pode rodar antes de timeout.
     * 10 minutos para permitir geração de PDFs grandes.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * O número de tentativas do job.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * O número de segundos para esperar antes de tentar novamente.
     *
     * @var int
     */
    public $backoff = 60;

    private GetPurchasesReportsRequestsAction $getPurchasesReportsRequestsAction;

    private GenerateMonthlyPurchasesReport $generateMonthlyPurchasesReport;

    private GenerateMonthlyReceiptsPurchaseReport $generateMonthlyReceiptsPurchaseReport;

    private GetPlansAction $getPlansAction;

    private GetChurchesAction $getChurchesAction;

    private GetChurchesByPlanIdAction $getChurchesByPlanIdAction;

    private GetPurchasesReportsRequestsByStatusAction $getPurchasesReportsRequestsByStatusAction;

    private UpdateStatusPurchasesReportRequestsAction $updateStatusPurchasesReportRequestsAction;

    public function __construct() {}

    /**
     * @throws GeneralExceptions
     * @throws BindingResolutionException|Throwable
     */
    public function handle(
        GetPurchasesReportsRequestsAction $getPurchasesReportsRequestsAction,
        GenerateMonthlyPurchasesReport $generateMonthlyPurchasesReport,
        GenerateMonthlyReceiptsPurchaseReport $generateMonthlyReceiptsPurchaseReport,
        GetPlansAction $getPlansAction,
        GetChurchesAction $getChurchesAction,
        GetChurchesByPlanIdAction $getChurchesByPlanIdAction,
        UpdateStatusPurchasesReportRequestsAction $updateStatusPurchasesReportRequestsAction,
        GetPurchasesReportsRequestsByStatusAction $getPurchasesReportsRequestsByStatusAction,
    ): void {
        $tenants = $this->getActiveTenants($getChurchesAction);

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            $reports = $getPurchasesReportsRequestsByStatusAction->execute(MonthlyPurchasesReportsRepository::TO_PROCESS_STATUS_VALUE);

            if (count($reports) > 0) {
                foreach ($reports as $report) {
                    try {
                        $updateStatusPurchasesReportRequestsAction->execute($report->id, MonthlyPurchasesReportsRepository::IN_PROGRESS_STATUS_VALUE);

                        if ($report->reportName == MonthlyPurchasesReportsRepository::MONTHLY_PURCHASES_REPORT_NAME) {
                            $generateMonthlyPurchasesReport->execute($report, $tenant);
                        }

                        if ($report->reportName == MonthlyPurchasesReportsRepository::MONTHLY_RECEIPTS_PURCHASE_REPORT_NAME) {
                            $generateMonthlyReceiptsPurchaseReport->execute($report, $tenant);
                        }
                    } catch (GeneralExceptions $e) {
                        if ($e->getCode() == 404) {
                            $updateStatusPurchasesReportRequestsAction->execute($report->id, MonthlyPurchasesReportsRepository::NO_DATA_STATUS_VALUE);
                        }

                        if ($e->getCode() == 500) {
                            $updateStatusPurchasesReportRequestsAction->execute($report->id, MonthlyPurchasesReportsRepository::ERROR_STATUS_VALUE);
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
                $arrTenants[] = $tenant->tenantId;
            }
        }

        return $arrTenants;
    }
}
