<?php

namespace Application\Api\v1\Members\Controllers;

use App\Domain\Members\Constants\ReturnMessages;
use Application\Api\v1\Members\Requests\MemberAvatarRequest;
use Application\Api\v1\Members\Requests\MemberRequest;
use Application\Api\v1\Members\Resources\MemberResource;
use Application\Api\v1\Members\Resources\MemberResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Members\Actions\CreateMemberAction;
use Domain\Members\Actions\GetMemberByIdAction;
use Domain\Members\Actions\GetMembersAction;
use Domain\Members\Actions\GetMembersCountersAction;
use Domain\Members\Actions\UpdateStatusMemberAction;
use Domain\Members\Actions\UpdateMemberAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class MemberController extends Controller
{

    /**
     * @param MemberRequest $memberRequest
     * @param CreateMemberAction $createMemberAction
     * @return Response
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function createMember(MemberRequest $memberRequest, CreateMemberAction $createMemberAction): Response
    {
        try
        {
            $createMemberAction($memberRequest->memberData());

            return response([
                'message'   =>  ReturnMessages::SUCCESS_MEMBER_REGISTERED,
            ], 201);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @param GetMembersAction $getMembersAction
     * @return MemberResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getMembers(GetMembersAction $getMembersAction): MemberResourceCollection
    {
        try
        {
            $response = $getMembersAction();
            return new MemberResourceCollection($response);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetMembersCountersAction $getMembersCountersAction
     * @return Response
     * @throws Throwable
     */
    public function getCounters(Request $request, GetMembersCountersAction $getMembersCountersAction): Response
    {
        try
        {
            $response = $getMembersCountersAction($request->input('key'));

            if($response)
            {
                return response(
                    [
                    'data'   =>  [
                        'counter'   =>  $response['counter']
                    ]], 200);
            }

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @throws GeneralExceptions|Throwable
     */
    public function getMemberById($id, GetMemberByIdAction $getMemberByIdAction): MemberResource
    {
        try
        {
            $response = $getMemberByIdAction($id);
            return new MemberResource($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @param Request $request
     * @param $id
     * @param UpdateStatusMemberAction $updateStatusMemberAction
     * @return Response
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function updateStatus(Request $request, $id, UpdateStatusMemberAction $updateStatusMemberAction): Response
    {
        try
        {
            $activated = $request->input('activated');
            $response = $updateStatusMemberAction($id, $activated);
            if($response)
            {
                return response([
                    'message'   =>  ReturnMessages::SUCCESS_UPDATE_STATUS_MEMBER,
                ], 200);
            }
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @param MemberRequest $memberRequest
     * @param $id
     * @param UpdateMemberAction $updateMemberAction
     * @return Response
     * @throws GeneralExceptions
     * @throws UnknownProperties|BindingResolutionException
     */
    public function updateMember(MemberRequest $memberRequest, $id, UpdateMemberAction $updateMemberAction): Response
    {
        try
        {
            $response = $updateMemberAction($id, $memberRequest->memberData());

            if($response)
            {
                return response([
                    'message'   =>  ReturnMessages::SUCCESS_UPDATED_MEMBER,
                ], 200);
            }
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param MemberAvatarRequest $memberAvatarRequest
     * @param UploadFile $uploadFile
     * @return Response
     * @throws GeneralExceptions
     */
    public function uploadMemberAvatar(MemberAvatarRequest $memberAvatarRequest, UploadFile $uploadFile): Response
    {
        try
        {
            $tenantS3PathObject = 'members/assets/avatars';
            $tenant = explode('.', $memberAvatarRequest->getHost())[0];
            $response = $uploadFile->upload($memberAvatarRequest->files->get('avatar'), $tenantS3PathObject, $tenant);

            if($response)
                return response([
                    'message'   => ReturnMessages::SUCCESS_UPDATE_IMAGE_MEMBER,
                    'avatar'    =>  $response
                ], 200);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
