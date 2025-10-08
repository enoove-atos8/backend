<?php

namespace Application\Api\v1\Financial\Entries\Reports\Controllers;

use App\Domain\Financial\Entries\Consolidation\Actions\GetEntriesEvolutionConsolidatedAction;
use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\MonthlyReportData;
use Application\Api\v1\Financial\Entries\Entries\Resources\EntriesEvolutionConsolidationResourceCollection;
use Application\Api\v1\Financial\Entries\Reports\Requests\MonthlyEntriesReportRequest;
use Application\Api\v1\Financial\Entries\Reports\Requests\MonthlyReceiptsReportRequest;
use Application\Api\v1\Financial\Entries\Reports\Resources\ReportsRequestsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Entries\Reports\Actions\CreateMonthlyEntriesReportAction;
use Domain\Financial\Entries\Reports\Actions\CreateMonthlyReceiptsReportAction;
use Domain\Financial\Entries\Reports\Actions\GetReportsRequestsAction;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
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
