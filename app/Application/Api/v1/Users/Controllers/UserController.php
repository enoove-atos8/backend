<?php

namespace Application\Api\v1\Users\Controllers;

use App\Domain\Accounts\Users\Actions\CreateUserAction;
use App\Domain\Accounts\Users\Actions\DeleteUserAction;
use App\Domain\Accounts\Users\Actions\GetUsersAction;
use App\Domain\Accounts\Users\Actions\GetUserByIdAction;
use App\Domain\Accounts\Users\Actions\UpdateUserAction;
use App\Domain\Accounts\Users\Actions\UpdateStatusUserAction;
use App\Domain\Auth\Actions\ChangePasswordAction;
use Application\Api\v1\Users\Requests\ChangePasswordRequest;
use Application\Api\v1\Users\Requests\UserAvatarRequest;
use Application\Api\v1\Users\Requests\UserRequest;
use Application\Core\Http\Controllers\Controller;
use Http\Client\Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Application\Api\v1\Users\Resources\UserResourceCollection;
use Application\Api\v1\Users\Resources\UserResource;
use App\Domain\Accounts\Users\Constants\ReturnMessages;
use Throwable;

class UserController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:admin|pastor']);
    }

    /**
     * @param UserRequest $userRequest
     * @return Response
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws UnknownProperties
     */
    public function createUser(UserRequest $userRequest, CreateUserAction $createUserAction): Response
    {
        try
        {
            $tenant = explode('.', $userRequest->getHost())[0];

            $createUserAction->execute(
                $userRequest->userData(),
                $userRequest->userDetailData(),
                $tenant);

            return response([
                'message'   =>  ReturnMessages::SUCCESS_USER_REGISTERED,
            ], 201);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param \App\Domain\Users\User\Actions\GetUsersAction $getUsersAction
     * @return UserResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getUsers(GetUsersAction $getUsersAction): UserResourceCollection
    {
        try
        {
            $response = $getUsersAction->execute();
            return new UserResourceCollection($response);
        }
        catch (GeneralExceptions $e)
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
            $response = $getUserByIdAction->execute($id);
            return new UserResource($response);

        }
        catch (GeneralExceptions $e)
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
            $response = $updateStatusUserAction->execute($id, $request->input('status'));
            if($response)
            {
                return response([
                    'message'   =>  ReturnMessages::SUCCESS_UPDATE_STATUS_USER,
                ], 200);
            }

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param UserRequest $userRequest
     * @param $id
     * @return Response
     * @throws GeneralExceptions|UnknownProperties|BindingResolutionException
     */
    public function updateUser(UserRequest $userRequest, $id, UpdateUserAction $updateUserAction): Response
    {
        try
        {
            $response = $updateUserAction->execute($id, $userRequest->userData(), $userRequest->userDetailData());

            if($response)
            {
                return response([
                    'message'   =>  ReturnMessages::SUCCESS_UPDATED_USER,
                ], 201);
            }

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param UserAvatarRequest $userAvatarRequest
     * @param UploadFile $uploadFile
     * @return Response
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function uploadUserAvatar(UserAvatarRequest $userAvatarRequest, UploadFile $uploadFile): Response
    {
        try
        {
            $tenantS3PathObject = 'users/assets/avatars';
            $tenant = explode('.', $userAvatarRequest->getHost())[0];
            $response = $uploadFile->upload($userAvatarRequest->files->get('avatar'), $tenantS3PathObject, $tenant);

            if($response)
                return response([
                    'message'   => ReturnMessages::SUCCESS_UPDATE_IMAGE_USER,
                    'avatar'    =>  $response
                ], 200);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param ChangePasswordRequest $changePasswordRequest
     * @param ChangePasswordAction $changePasswordAction
     * @return Response
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function changePassword(ChangePasswordRequest $changePasswordRequest, ChangePasswordAction $changePasswordAction): Response
    {
        try
        {
            $response = $changePasswordAction->execute($changePasswordRequest->changePasswordData());

            if ($response) {
                return response([
                    'message' => ReturnMessages::SUCCESS_CHANGE_PASSWORD,
                ], 200);
            }

            throw new GeneralExceptions(ReturnMessages::ERROR_CHANGE_PASSWORD, 500);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function deleteUser(int $id, DeleteUserAction $deleteUserAction): Response
    {
        try {
            $result = $deleteUserAction->execute($id);

            if ($result) {
                return response([
                    'message' => ReturnMessages::SUCCESS_USER_DELETED,
                ], 200);
            }

            return response([
                'message' => ReturnMessages::INFO_NO_USER_FOUNDED,
            ], 404);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
