<?php

namespace Application\Api\v1\Users\Controllers;

use Application\Api\v1\Users\Requests\UserAvatarRequest;
use Application\Api\v1\Users\Requests\UserRequest;
use Application\Api\v1\Users\Resources\ErrorUserResource;
use Application\Api\v1\Users\Resources\UserResource;
use Application\Api\v1\Users\Resources\UserResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Users\Actions\CreateUserAction;
use Domain\Users\Actions\GetUserByIdAction;
use Domain\Users\Actions\GetUsersAction;
use Domain\Users\Actions\UpdateStatusUserAction;
use Domain\Users\Actions\UpdateUserAction;
use Domain\Users\Actions\UploadUserAvatarAction;
use Http\Client\Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class UserController extends Controller
{

    /**
     * @param UserRequest $userRequest
     * @param CreateUserAction $createUserAction
     * @return Response
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function createUser(UserRequest $userRequest, CreateUserAction $createUserAction): Response
    {
        try
        {
            $createUserAction($userRequest->userData(), $userRequest->userDetailData());

            return response([
                'message'   =>  'Usuário cadastrado com sucesso!',
            ], 201);

        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param GetUsersAction $getUsersAction
     * @return UserResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getUsers(GetUsersAction $getUsersAction): UserResourceCollection
    {
        try
        {
            $response = $getUsersAction();
            return new UserResourceCollection($response);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @throws GeneralExceptions|Throwable
     */
    public function getUserById($id, GetUserByIdAction $getUserByIdAction): UserResource
    {
        try
        {
            $response = $getUserByIdAction($id);
            return new UserResource($response);

        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param $id
     * @param UpdateStatusUserAction $updateStatusUserAction
     * @return Response
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function updateStatus(Request $request, $id, UpdateStatusUserAction $updateStatusUserAction): Response
    {
        try
        {
            $response = $updateStatusUserAction($id, $request->input('status'));
            return response([
                'message'   =>  'Status do usuário atualizado com sucesso!',
            ], 200);

        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param UserRequest $userRequest
     * @param $id
     * @param UpdateUserAction $updateUserAction
     * @return Response
     * @throws GeneralExceptions|UnknownProperties|BindingResolutionException
     */
    public function updateUser(UserRequest $userRequest, $id, UpdateUserAction $updateUserAction): Response
    {
        try
        {
            $updateUserAction($id, $userRequest->userData(), $userRequest->userDetailData());
            return response([
                'message'   =>  'Usuário atualizado com sucesso!',
            ], 201);

        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param UserAvatarRequest $userAvatarRequest
     * @param UploadUserAvatarAction $uploadUserAvatarAction
     * @return Response
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function uploadUserAvatar(UserAvatarRequest $userAvatarRequest, UploadUserAvatarAction $uploadUserAvatarAction): Response
    {
        try
        {
            $tenant = explode('.', $userAvatarRequest->getHost())[0];
            $response = $uploadUserAvatarAction($userAvatarRequest->files->get('avatar'), $tenant);

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
