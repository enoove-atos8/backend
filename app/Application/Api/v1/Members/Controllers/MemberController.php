<?php

namespace Application\Api\v1\Members\Controllers;

use Application\Api\v1\Members\Requests\MemberAvatarRequest;
use Application\Api\v1\Members\Requests\MemberRequest;
use Application\Api\v1\Members\Resources\ErrorMemberResource;
use Application\Api\v1\Members\Resources\MemberResource;
use Application\Api\v1\Members\Resources\MemberResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Members\Actions\CreateMemberAction;
use Domain\Members\Actions\GetMemberByIdAction;
use Domain\Members\Actions\GetMembersAction;
use Domain\Members\Actions\UpdateStatusMemberAction;
use Domain\Members\Actions\UpdateMemberAction;
use Domain\Members\Actions\UploadMemberAvatarAction;
use Http\Client\Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
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
                'message'   =>  'Membro cadastrado com sucesso!',
            ], 201);

        }
        catch (Exception $e)
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
        catch (Exception $e)
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
        catch (Exception $e)
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
            $response = $updateStatusMemberAction($id, $request->input('status'));
            return response([
                'message'   =>  'Status do membro atualizado com sucesso!',
            ], 200);

        }
        catch (Exception $e)
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
     * @throws UnknownProperties
     */
    public function updateMember(MemberRequest $memberRequest, $id, UpdateMemberAction $updateMemberAction): Response
    {
        try
        {
            $updateMemberAction($id, $memberRequest->memberData());
            return response([
                'message'   =>  'Membro atualizado com sucesso!',
            ], 201);

        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @param MemberAvatarRequest $memberAvatarRequest
     * @param UploadMemberAvatarAction $uploadMemberAvatarAction
     * @return Response
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function uploadMemberAvatar(MemberAvatarRequest $memberAvatarRequest, UploadMemberAvatarAction $uploadMemberAvatarAction): Response
    {
        try
        {
            $tenant = explode('.', $memberAvatarRequest->getHost())[0];
            $response = $uploadMemberAvatarAction($memberAvatarRequest->files->get('avatar'), $tenant);

            if($response)
                return response([
                    'avatar'    =>  $response
                ], 200);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
