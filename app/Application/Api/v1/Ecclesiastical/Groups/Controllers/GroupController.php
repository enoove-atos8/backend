<?php

namespace Application\Api\v1\Ecclesiastical\Groups\Controllers;

use Application\Api\v1\Ecclesiastical\Groups\Requests\GroupRequest;
use Application\Api\v1\Ecclesiastical\Groups\Resources\GroupResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionIdByName;
use Domain\Ecclesiastical\Groups\Actions\CreateNewGroupAction;
use Domain\Ecclesiastical\Groups\Actions\GetGroupsByDivisionAction;
use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\Models\Group;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class GroupController extends Controller
{
    /**
     *
     * @param GroupRequest $groupRequest
     * @param CreateNewGroupAction $createNewGroupAction
     * @return Application|Response|ResponseFactory
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function createGroup(GroupRequest $groupRequest, CreateNewGroupAction $createNewGroupAction): Application|Response|ResponseFactory
    {
        try
        {
            $createNewGroupAction($groupRequest->groupData());

            return response([
                'message'   =>  ReturnMessages::GROUP_CREATED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetGroupsByDivisionAction $getGroupsByDivisionAction
     * @param GetDivisionIdByName $getDivisionIdByName
     * @return GroupResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getGroupsByDivision(Request $request, GetGroupsByDivisionAction $getGroupsByDivisionAction, GetDivisionIdByName $getDivisionIdByName): GroupResourceCollection
    {
        try
        {
            $divisionParam = $request->input('division');
            $division = $getDivisionIdByName($divisionParam);
            $response = $getGroupsByDivisionAction($divisionParam);

            return new GroupResourceCollection($response, $division);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
