<?php

namespace Application\Api\v1\Ecclesiastical\Groups\Controllers;

use Application\Api\v1\Ecclesiastical\Groups\Resources\GroupResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Groups\Actions\GetGroupsByDivisionAction;
use Domain\Ecclesiastical\Groups\Models\Group;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class GroupController extends Controller
{
    /**
     * @param Request $request
     * @param GetGroupsByDivisionAction $getGroupsByDivisionAction
     * @return GroupResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getGroupsByDivision(Request $request, GetGroupsByDivisionAction $getGroupsByDivisionAction): GroupResourceCollection
    {
        try
        {
            $division = $request->input('division');
            $response = $getGroupsByDivisionAction($division);

            return new GroupResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
