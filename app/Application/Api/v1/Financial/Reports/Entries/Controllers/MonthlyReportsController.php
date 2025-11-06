<?php

namespace App\Application\Api\v1\Financial\Reports\Entries\Controllers;

use App\Application\Api\v1\Financial\Reports\Entries\Requests\MonthlyEntriesReportRequest;
use App\Application\Api\v1\Financial\Reports\Entries\Requests\MonthlyReceiptsReportRequest;
use App\Application\Api\v1\Financial\Reports\Entries\Resources\ReportsRequestsResourceCollection;
use App\Domain\Financial\Reports\Entries\Actions\CreateMonthlyEntriesReportAction;
use App\Domain\Financial\Reports\Entries\Actions\CreateMonthlyReceiptsReportAction;
use App\Domain\Financial\Reports\Entries\Actions\GetReportsRequestsAction;
use App\Domain\Financial\Reports\Entries\Constants\ReturnMessages;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class MonthlyReportsController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getReports(GetReportsRequestsAction $getReportsAction): ReportsRequestsResourceCollection
    {
        try
        {
            $response = $getReportsAction->execute();

            return new ReportsRequestsResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @throws GeneralExceptions|UnknownProperties
     */
    public function generateMonthlyReceiptsReport(MonthlyReceiptsReportRequest $monthlyReceiptsReportRequest, CreateMonthlyReceiptsReportAction $createMonthlyReceiptsReportAction): Application|Response|ResponseFactory
    {
        try
        {
            $createMonthlyReceiptsReportAction->execute($monthlyReceiptsReportRequest->monthlyReportData());

            return response([
                'message'   =>  ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS,
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
    public function generateMonthlyEntriesReport(MonthlyEntriesReportRequest $monthlyEntriesReportRequest, CreateMonthlyEntriesReportAction $createMonthlyEntriesReportAction): Application|Response|ResponseFactory
    {
        try
        {
            $createMonthlyEntriesReportAction->execute($monthlyEntriesReportRequest->monthlyReportData());

            return response([
                'message'   =>  ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS,
            ], 201);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
