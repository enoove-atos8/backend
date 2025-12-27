<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Controllers;

use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Requests\GroupRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Requests\UpdateGroupLeaderRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Requests\UpdateGroupStatusRequest;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\AllGroupsByDivisionResourceCollection;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupResource;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupResourceCollection;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupsToMobileAppResourceCollection;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources\GroupsWithDivisionsResourceCollection;
use App\Domain\Secretary\Membership\Actions\AddMembersToGroupAction;
use Application\Api\v1\Secretary\Membership\Membership\Requests\AddMembersToGroupRequest;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionByNameAction;
use Domain\Ecclesiastical\Groups\Actions\CreateNewGroupAction;
use Domain\Ecclesiastical\Groups\Actions\DeleteGroupAction;
use Domain\Ecclesiastical\Groups\Actions\ExportMovementsGroupAction;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsAction;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsByAllDivisionsAction;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsWithDivisionsAction;
use Domain\Ecclesiastical\Groups\Actions\GetGroupByIdAction;
use Domain\Ecclesiastical\Groups\Actions\GetGroupsByDivisionAction;
use Domain\Ecclesiastical\Groups\Actions\UpdateGroupLeaderAction;
use Domain\Ecclesiastical\Groups\Actions\UpdateGroupStatusAction;
use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
use Domain\Secretary\Membership\Constants\ReturnMessages as MembershipReturnMessages;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class GroupController extends Controller
{
    /**
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function createGroup(GroupRequest $groupRequest, CreateNewGroupAction $createNewGroupAction): Application|Response|ResponseFactory
    {
        try {
            $tenant = explode('.', $groupRequest->getHost())[0];
            $createNewGroupAction->execute($groupRequest->groupData(), $tenant);

            return response([
                'message' => ReturnMessages::GROUP_CREATED,
            ], 201);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getGroupsByDivision(Request $request, GetGroupsByDivisionAction $getGroupsByDivisionAction, GetDivisionByNameAction $getDivisionByName): GroupResourceCollection
    {
        try {
            $divisionParam = $request->input('division');
            $activeParam = $request->input('active');
            $active = $activeParam !== null ? filter_var($activeParam, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;

            $division = $getDivisionByName->execute($divisionParam);
            $response = $getGroupsByDivisionAction->execute($divisionParam, $active);

            return new GroupResourceCollection($response, $division);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function getGroupById(Request $request, GetGroupByIdAction $getGroupByIdAction): GroupResource
    {
        try {
            $id = $request->input('id');
            $response = $getGroupByIdAction->execute($id);

            return new GroupResource($response);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function getAllGroups(Request $request, GetAllGroupsAction $getAllGroupsAction): GroupResourceCollection
    {
        try {
            $response = $getAllGroupsAction->execute();

            return new GroupResourceCollection($response);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getAllGroupsWithDivisions(Request $request, GetAllGroupsWithDivisionsAction $getAllGroupsWithDivisionsAction): GroupsWithDivisionsResourceCollection
    {
        try {
            $response = $getAllGroupsWithDivisionsAction->execute();

            return new GroupsWithDivisionsResourceCollection($response);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getAllGroupsByAllDivisions(
        Request $request,
        GetAllGroupsByAllDivisionsAction $getAllGroupsByAllDivisionsAction
    ): AllGroupsByDivisionResourceCollection {
        try {
            $response = $getAllGroupsByAllDivisionsAction->execute();

            return new AllGroupsByDivisionResourceCollection($response);

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getGroupsToMobileApp(Request $request, GetGroupsByDivisionAction $getGroupsByDivisionAction): ResponseFactory|Application|Response|GroupsToMobileAppResourceCollection
    {
        try {
            $division = $request->input('division');
            $activeParam = $request->input('active');
            $active = $activeParam !== null ? filter_var($activeParam, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;

            $response = $getGroupsByDivisionAction->execute($division, $active);

            if (count($response) == 0) {
                return response(['message' => 'NADA'], 404);
            } else {
                return new GroupsToMobileAppResourceCollection($response);
            }

        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Export movements group data to PDF or XLSX
     *
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function exportMovementsGroupData(Request $request, ExportMovementsGroupAction $exportMovementsGroupAction): JsonResponse
    {
        try {
            $groupId = (int) $request->input('id');
            $dates = $request->input('dates', 'all');
            $type = $request->input('type', 'PDF');
            $paginate = false;

            $result = $exportMovementsGroupAction->execute($groupId, $dates, $type, $paginate);

            return response()->json($result);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function addMembersToGroup(AddMembersToGroupRequest $request, AddMembersToGroupAction $addMembersToGroupAction): Response
    {
        try {
            $groupId = (int) $request->input('groupId');
            $memberIds = $request->input('memberIds');

            $success = $addMembersToGroupAction->execute($groupId, $memberIds);

            if ($success) {
                return response([
                    'message' => MembershipReturnMessages::SUCCESS_MEMBERS_ADDED_TO_GROUP,
                ], 200);
            }

            return response([
                'message' => MembershipReturnMessages::ERROR_ADD_MEMBERS_TO_GROUP,
            ], 500);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function updateLeader(
        int $id,
        UpdateGroupLeaderRequest $request,
        UpdateGroupLeaderAction $action
    ): Response {
        try {
            $leaderId = $request->input('leader_id');

            $action->execute($id, $leaderId);

            return response([
                'message' => ReturnMessages::GROUP_LEADER_UPDATED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function updateStatus(
        int $id,
        UpdateGroupStatusRequest $request,
        UpdateGroupStatusAction $action
    ): Response {
        try {
            $enabled = (bool) $request->input('enabled');

            $action->execute($id, $enabled);

            return response([
                'message' => ReturnMessages::GROUP_STATUS_UPDATED,
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions|Throwable
     */
    public function destroy(
        int $id,
        DeleteGroupAction $action
    ): Response {
        try {
            $result = $action->execute($id);

            if (! $result['success']) {
                return response([
                    'message' => $result['message'],
                    'balance' => $result['balance'] ?? null,
                ], 400);
            }

            return response([
                'message' => $result['message'],
            ], 200);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
