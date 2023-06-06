<?php

namespace App\Application\Api\v1\Auth\Controllers;

use App\Application\Api\v1\Auth\Requests\AuthRequest;
use App\Application\Api\v1\Auth\Resources\ErrorLoginResource;
use App\Application\Api\v1\Auth\Resources\LoginResource;
use Application\Core\Http\Controllers\Controller;
use Domain\Auth\Actions\LoginAction;
use Domain\Auth\Actions\LogoutAction;
use Illuminate\Http\JsonResponse;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/v1/login",
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
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function login(AuthRequest $authRequest, LoginAction $loginAction): JsonResponse|LoginResource
    {
        $response = $loginAction($authRequest->authData());

        //$test = Crypt::encryptString('Jesus100');
        //$test2 = Crypt::decryptString($test);

        if (is_array($response) && array_key_exists('error',$response))
            return (new ErrorLoginResource($response))->response()->setStatusCode($response["status"]);
        else
            return new LoginResource($response);
    }

    public function logout(LogoutAction $logoutAction)
    {
        return $logoutAction();
    }

    /**
     * Verify if current token is valid
     */
}
