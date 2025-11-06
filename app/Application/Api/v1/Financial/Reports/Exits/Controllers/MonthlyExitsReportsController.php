<?php

namespace App\Application\Api\v1\Financial\Reports\Exits\Controllers;

use App\Application\Api\v1\Financial\Reports\Exits\Requests\MonthlyExitsReceiptsReportRequest;
use App\Application\Api\v1\Financial\Reports\Exits\Requests\MonthlyExitsReportRequest;
use App\Application\Api\v1\Financial\Reports\Exits\Resources\ExitsReportsRequestsResourceCollection;
use App\Domain\Financial\Reports\Exits\Actions\CreateMonthlyExitsReceiptsReportAction;
use App\Domain\Financial\Reports\Exits\Actions\CreateMonthlyExitsReportAction;
use App\Domain\Financial\Reports\Exits\Actions\GetExitsReportsRequestsAction;
use Application\Core\Http\Controllers\Controller;
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
