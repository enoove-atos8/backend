<?php

namespace Application\Api\v1\Financial\Movements\Controllers;

use Application\Api\v1\Financial\Movements\Resources\MovementResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Movements\Actions\GetMovementsByGroupAction;
use Domain\Financial\Movements\Actions\GetMovementsIndicatorsAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;
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
}
