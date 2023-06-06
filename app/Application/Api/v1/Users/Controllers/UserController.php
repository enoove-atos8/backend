<?php

namespace Application\Api\v1\Users\Controllers;

use Application\Api\v1\Users\Requests\UserRequest;
use Application\Api\v1\Users\Resources\ErrorUserResource;
use Application\Api\v1\Users\Resources\UserResource;
use Application\Api\v1\Users\Resources\UserResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Users\Actions\CreateUserAction;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserController extends Controller
{

    /**
     * @OA\Post(
     * path="/v1/user",
     * security={{"Bearer": {}}},
     * tags={"Login"},
     * summary="User Login",
     * description="User Login here",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *          @OA\Property(property="email", type="string", pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$", format="email", example="test@gmail.com"),
     *          @OA\Property(property="password", type="string", format="password")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Login Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Login Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=422,description="Unprocessable Entity"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param UserRequest $userRequest
     * @param CreateUserAction $createUserAction
     * @return UserResource
     * @throws UnknownProperties
     */
    public function createUser(UserRequest $userRequest, CreateUserAction $createUserAction): UserResource
    {
        $response = $createUserAction($userRequest->userData());
        return new UserResource($response);
    }
}
