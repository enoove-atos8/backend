<?php

namespace App\Application\Api\v1\Financial\Reports\Balances\Controllers;

use App\Application\Api\v1\Financial\Reports\Balances\Requests\MonthlyBalancesReportRequest;
use App\Application\Api\v1\Financial\Reports\Balances\Resources\BalancesReportsRequestsResourceCollection;
use App\Domain\Financial\Reports\Balances\Actions\CreateMonthlyBalancesReportAction;
use App\Domain\Financial\Reports\Balances\Actions\GetBalancesReportsRequestsAction;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class MonthlyBalancesReportsController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getReports(GetBalancesReportsRequestsAction $getBalancesReportsAction): BalancesReportsRequestsResourceCollection
    {
        try {
            $response = $getBalancesReportsAction->execute();

            return new BalancesReportsRequestsResourceCollection($response);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|UnknownProperties
     */
    public function generateMonthlyBalancesReport(MonthlyBalancesReportRequest $monthlyBalancesReportRequest, CreateMonthlyBalancesReportAction $createMonthlyBalancesReportAction): Application|Response|ResponseFactory
    {
        try {
            $createMonthlyBalancesReportAction->execute($monthlyBalancesReportRequest->monthlyBalancesReportData());

            return response([
                'message' => 'Report request successfully sent to process',
            ], 201);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
