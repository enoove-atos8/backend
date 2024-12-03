<?php

namespace Application\Api\v1\Financial\Entries\Consolidation\Controllers;


use App\Domain\Financial\Entries\Cults\Constants\ReturnMessages;
use Application\Api\v1\Financial\Entries\Consolidation\Resources\ConsolidationResourceCollection;
use Application\Api\v1\Financial\Entries\Cults\Requests\CultRequest;
use Application\Api\v1\Financial\Entries\Cults\Resources\CultsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Entries\Consolidation\Actions\GetMonthsAction;
use Domain\Financial\Entries\Cults\Actions\CreateCultAction;
use Domain\Financial\Entries\Cults\Actions\GetCultsAction;
use Domain\Financial\Entries\Entries\Actions\GetTotalAmountEntriesAction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class ConsolidationController extends Controller
{
    /**
     * @throws Throwable
     * @throws GeneralExceptions
     */
    public function getMonths(GetMonthsAction $getMonthsAction): ConsolidationResourceCollection
    {
        try
        {
            $response = $getMonthsAction();
            return new ConsolidationResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * Create new cult
     * @return Application|ResponseFactory|Response
     * @throws GeneralExceptions
     */
    public function consolidateMonth(): Application|ResponseFactory|Response
    {
        try
        {


            return response([
                'message'   =>  ReturnMessages::SUCCESS_CULT_REGISTERED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * Create new cult
     * @return Application|ResponseFactory|Response
     * @throws GeneralExceptions
     */
    public function reopenMonth(): Application|ResponseFactory|Response
    {
        try
        {


            return response([
                'message'   =>  ReturnMessages::SUCCESS_CULT_REGISTERED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getTotalAmountEntries(Request $request, GetTotalAmountEntriesAction $getTotalAmountEntriesAction): Application|Response|ResponseFactory
    {
        try
        {
            $date = $request->input('date');
            $response = $getTotalAmountEntriesAction($date);

            return response($response, 201);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
