<?php

namespace App\Application\Api\v1\Financial\Reports\Purchases\Controllers;

use App\Application\Api\v1\Financial\Reports\Purchases\Requests\MonthlyPurchasesReportRequest;
use App\Application\Api\v1\Financial\Reports\Purchases\Requests\MonthlyReceiptsPurchaseReportRequest;
use App\Application\Api\v1\Financial\Reports\Purchases\Resources\PurchasesReportsRequestsResourceCollection;
use App\Domain\Financial\Reports\Purchases\Actions\CreateMonthlyPurchasesReportAction;
use App\Domain\Financial\Reports\Purchases\Actions\CreateMonthlyReceiptsPurchaseReportAction;
use App\Domain\Financial\Reports\Purchases\Actions\GetPurchasesReportsRequestsAction;
use App\Domain\Financial\Reports\Purchases\Constants\ReturnMessages;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class MonthlyPurchasesReportsController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getReports(GetPurchasesReportsRequestsAction $getPurchasesReportsAction): PurchasesReportsRequestsResourceCollection
    {
        try
        {
            $response = $getPurchasesReportsAction->execute();

            return new PurchasesReportsRequestsResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @throws GeneralExceptions|UnknownProperties
     */
    public function generateMonthlyPurchasesReport(MonthlyPurchasesReportRequest $monthlyPurchasesReportRequest, CreateMonthlyPurchasesReportAction $createMonthlyPurchasesReportAction): Application|Response|ResponseFactory
    {
        try
        {
            $createMonthlyPurchasesReportAction->execute($monthlyPurchasesReportRequest->monthlyPurchasesReportData());

            return response([
                'message' => ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS,
            ], 201);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @throws GeneralExceptions|UnknownProperties
     */
    public function generateMonthlyReceiptsPurchaseReport(MonthlyReceiptsPurchaseReportRequest $monthlyReceiptsPurchaseReportRequest, CreateMonthlyReceiptsPurchaseReportAction $createMonthlyReceiptsPurchaseReportAction): Application|Response|ResponseFactory
    {
        try
        {
            $createMonthlyReceiptsPurchaseReportAction->execute($monthlyReceiptsPurchaseReportRequest->monthlyPurchasesReportData());

            return response([
                'message' => ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS,
            ], 201);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
