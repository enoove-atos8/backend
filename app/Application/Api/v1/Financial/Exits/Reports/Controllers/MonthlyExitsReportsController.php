<?php

namespace Application\Api\v1\Financial\Exits\Reports\Controllers;

use Application\Api\v1\Financial\Exits\Reports\Requests\MonthlyExitsReceiptsReportRequest;
use Application\Api\v1\Financial\Exits\Reports\Requests\MonthlyExitsReportRequest;
use Application\Api\v1\Financial\Exits\Reports\Resources\ExitsReportsRequestsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Exits\Reports\Actions\CreateMonthlyExitsReceiptsReportAction;
use Domain\Financial\Exits\Reports\Actions\CreateMonthlyExitsReportAction;
use Domain\Financial\Exits\Reports\Actions\GetExitsReportsRequestsAction;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class MonthlyExitsReportsController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getReports(GetExitsReportsRequestsAction $getExitsReportsAction): ExitsReportsRequestsResourceCollection
    {
        try
        {
            $response = $getExitsReportsAction->execute();

            return new ExitsReportsRequestsResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @throws GeneralExceptions|UnknownProperties
     */
    public function generateMonthlyExitsReport(MonthlyExitsReportRequest $monthlyExitsReportRequest, CreateMonthlyExitsReportAction $createMonthlyExitsReportAction): Application|Response|ResponseFactory
    {
        try
        {
            $createMonthlyExitsReportAction->execute($monthlyExitsReportRequest->monthlyExitsReportData());

            return response([
                'message'   =>  'Report request successfully sent to process',
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
    public function generateMonthlyExitsReceiptsReport(MonthlyExitsReceiptsReportRequest $monthlyExitsReceiptsReportRequest, CreateMonthlyExitsReceiptsReportAction $createMonthlyExitsReceiptsReportAction): Application|Response|ResponseFactory
    {
        try
        {
            $createMonthlyExitsReceiptsReportAction->execute($monthlyExitsReceiptsReportRequest->monthlyExitsReportData());

            return response([
                'message'   =>  'Report request successfully sent to process',
            ], 201);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
