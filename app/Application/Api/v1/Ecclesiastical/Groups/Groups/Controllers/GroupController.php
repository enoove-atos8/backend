<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Controllers;

use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Requests\GroupRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupResource;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupResourceCollection;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupsToMobileAppResourceCollection;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupsWithDivisionsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionByNameAction;
use Domain\Ecclesiastical\Groups\Actions\CreateNewGroupAction;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsAction;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsWithDivisionsAction;
use Domain\Ecclesiastical\Groups\Actions\GetGroupByIdAction;
use Domain\Ecclesiastical\Groups\Actions\GetGroupsByDivisionAction;
use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
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
            $tenant = explode('.', $groupRequest->getHost())[0];
            $createNewGroupAction->execute($groupRequest->groupData(), $tenant);

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
     * @param GetDivisionByNameAction $getDivisionByName
     * @return GroupResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getGroupsByDivision(Request $request, GetGroupsByDivisionAction $getGroupsByDivisionAction, GetDivisionByNameAction $getDivisionByName): GroupResourceCollection
    {
        try
        {
            $divisionParam = $request->input('division');
            $division = $getDivisionByName->execute($divisionParam);
            $response = $getGroupsByDivisionAction->execute($divisionParam);

            return new GroupResourceCollection($response, $division);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetGroupByIdAction $getGroupByIdAction
     * @return GroupResource
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function getGroupById(Request $request, GetGroupByIdAction $getGroupByIdAction): GroupResource
    {
        try
        {
            $id = $request->input('id');
            $response = $getGroupByIdAction->execute($id);

            return new GroupResource($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetAllGroupsAction $getAllGroupsAction
     * @return GroupResourceCollection
     * @throws GeneralExceptions|Throwable
     */
    public function getAllGroups(Request $request, GetAllGroupsAction $getAllGroupsAction): GroupResourceCollection
    {
        try
        {
            $response = $getAllGroupsAction->execute();

            return new GroupResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetAllGroupsWithDivisionsAction $getAllGroupsWithDivisionsAction
     * @return \App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupsWithDivisionsResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getAllGroupsWithDivisions(Request $request, GetAllGroupsWithDivisionsAction $getAllGroupsWithDivisionsAction): GroupsWithDivisionsResourceCollection
    {
        try
        {
            $response = $getAllGroupsWithDivisionsAction->execute();

            return new GroupsWithDivisionsResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetGroupsByDivisionAction $getGroupsByDivisionAction
     * @return ResponseFactory|Application|Response|\App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupsToMobileAppResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getGroupsToMobileApp(Request $request, GetGroupsByDivisionAction $getGroupsByDivisionAction): ResponseFactory|Application|Response|GroupsToMobileAppResourceCollection
    {
        try
        {
            $division = $request->input('division');
            $response = $getGroupsByDivisionAction->execute($division);

            if(count($response) == 0)
                return response(['message' =>  'NADA'], 404);
            else
                return new GroupsToMobileAppResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
