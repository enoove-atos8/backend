<?php

namespace Application\Api\Users\Controllers;

use Application\Api\Users\Requests\UserRequest;
use Application\Api\Users\Resources\ErrorUserResource;
use Application\Api\Users\Resources\UserResource;
use Application\Api\Users\Resources\UserResourceCollection;
use Domain\Users\Actions\CreateUserAction;
use Domain\Users\Actions\ListUserAction;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ListUserAction $listUserAction
     * @return UserResourceCollection|JsonResponse
     */
    public function index(ListUserAction $listUserAction): UserResourceCollection|JsonResponse
    {
        $response = $listUserAction();

        if (is_array($response))
            return (new ErrorUserResource($response))->response()->setStatusCode($response["status"]);
        else
            return new UserResourceCollection($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param ListUserAction $listUserAction
     * @return UserResource
     */
    public function show(int $id, ListUserAction $listUserAction): UserResource
    {
        $response = $listUserAction($id);
        return new UserResource($response);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $userRequest
     * @param CreateUserAction $createUserAction
     * @return UserResource
     * @throws UnknownProperties
     */
    public function store(UserRequest $userRequest, CreateUserAction $createUserAction): UserResource
    {
        $response = $createUserAction($userRequest->userData());
        return new UserResource($response);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $userRequest
     * @param int $id
     * @return UserResource
     */
    public function update(UserRequest $userRequest, int $id): UserResource
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        //
    }
}
