<?php

namespace Application\Api\v1\Financial\Exits\Exits\Controllers;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Exits\Indicators\Actions\HandleExitsIndicatorsAction;
use Application\Api\v1\Financial\Entries\Entries\Resources\EntryResourceCollection;
use Application\Api\v1\Financial\Exits\Exits\Requests\ExitRequest;
use Application\Api\v1\Financial\Exits\Exits\Resources\AmountByExitTypeResource;
use Application\Api\v1\Financial\Exits\Exits\Resources\ExitsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsAction;
use Domain\Financial\Exits\Exits\Actions\CreateExitAction;
use Domain\Financial\Exits\Exits\Actions\DeleteExitAction;
use Domain\Financial\Exits\Exits\Actions\GetAmountByExitTypeAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Domain\Financial\Exits\Exits\Constants\ReturnMessages;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class ExitsController extends Controller
{
    /**
     * @param Request $request
     * @param GetExitsAction $getExitsAction
     * @param GetAllGroupsAction $getAllGroupsAction
     * @return ExitsResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getExits(Request $request, GetExitsAction $getExitsAction, GetAllGroupsAction $getAllGroupsAction): ExitsResourceCollection
    {
        try
        {
            $dates = $request->input('dates');
            $filters = $request->except(['dates','page']);
            $exits = $getExitsAction->execute($dates, $filters);

            return new ExitsResourceCollection($exits);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetAmountByExitTypeAction $getAmountByExitTypeAction
     * @return ResponseFactory|Application|Response|AmountByExitTypeResource
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getAmountByExitType(Request $request, GetAmountByExitTypeAction $getAmountByExitTypeAction): ResponseFactory|Application|Response | AmountByExitTypeResource
    {
        try
        {
            $date = $request->input('date');
            $exitType = $request->input('exitType');

            $response = $getAmountByExitTypeAction->execute($date, $exitType);

            return new AmountByExitTypeResource($response);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param HandleExitsIndicatorsAction $handleExitsIndicatorsAction
     * @return array
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getExitsAmount(Request $request, HandleExitsIndicatorsAction $handleExitsIndicatorsAction): array
    {
        try
        {
            $indicator = $request->input('indicator');
            $dates = $request->input('dates');
            $filters = $request->except(['dates', 'page', 'indicator']);

            return $handleExitsIndicatorsAction->execute($indicator, $dates, $filters);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     *
     * @param ExitRequest $exitRequest
     * @param CreateExitAction $createExitAction
     * @return Application|ResponseFactory|Response
     * @throws UnknownProperties
     * @throws Throwable
     */
    public function createExit(ExitRequest $exitRequest, CreateExitAction $createExitAction): Application|ResponseFactory|Response
    {
        try
        {
            $createExitAction->execute($exitRequest->exitData());

            return response([
                'message'   =>  ReturnMessages::SUCCESS_EXIT_REGISTERED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }




    /**
     *
     * @param $id
     * @param DeleteExitAction $deleteExitAction
     * @return Application|Response|ResponseFactory
     * @throws GeneralExceptions|Throwable
     */
    public function deleteExit($id, DeleteExitAction $deleteExitAction): Application|ResponseFactory|Response
    {
        try
        {
            $exitDeleted = $deleteExitAction->execute($id);

            if($exitDeleted)
            {
                return response([
                    'message'   =>  ReturnMessages::EXIT_DELETED,
                ], 200);
            }

        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
