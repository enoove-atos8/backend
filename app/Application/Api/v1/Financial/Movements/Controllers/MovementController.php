<?php

namespace Application\Api\v1\Financial\Movements\Controllers;

use Application\Api\v1\Financial\Movements\Requests\AddInitialBalanceRequest;
use Application\Api\v1\Financial\Movements\Resources\MovementResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Movements\Actions\CreateInitialMovementAction;
use Domain\Financial\Movements\Actions\GetMovementsByGroupAction;
use Domain\Financial\Movements\Actions\GetMovementsIndicatorsAction;
use Domain\Financial\Movements\Constants\ReturnMessages;
use Illuminate\Console\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class MovementController extends Controller
{
    /**
     * @param Request $request
     * @param GetMovementsByGroupAction $getMovementsByGroupAction
     * @return MovementResourceCollection
     * @throws GeneralExceptions
     */
    public function getMovementsByGroup(Request $request, GetMovementsByGroupAction $getMovementsByGroupAction): MovementResourceCollection
    {
        try
        {
            $id = $request->input('id');
            $dates = $request->input('dates');
            //$filters = $request->except(['dates','page']);
            $movements = $getMovementsByGroupAction->execute($id, $dates);

            return new MovementResourceCollection($movements);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @param Request $request
     * @param GetMovementsIndicatorsAction $getMovementsIndicatorsAction
     * @return JsonResponse
     * @throws GeneralExceptions
     */
    public function getMovementsIndicatorsByGroup(Request $request, GetMovementsIndicatorsAction $getMovementsIndicatorsAction): JsonResponse
    {
        try
        {
            $id = $request->input('id');
            $dates = $request->input('dates');
            $indicators = $getMovementsIndicatorsAction->execute($id, $dates);

            return response()->json($indicators);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * Add initial balance to a group
     *
     * @param AddInitialBalanceRequest $addInitialBalanceRequest
     * @param CreateInitialMovementAction $createInitialMovementAction
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function addInitialBalance(AddInitialBalanceRequest $addInitialBalanceRequest, CreateInitialMovementAction $createInitialMovementAction): ResponseFactory|Application|Response
    {
        try
        {
            $createInitialMovementAction->execute($addInitialBalanceRequest->movementsData());

            return response([
                'message'   =>  ReturnMessages::INITIAL_BALANCE_MOVEMENT_CREATED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
