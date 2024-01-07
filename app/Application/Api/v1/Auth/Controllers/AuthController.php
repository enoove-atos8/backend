<?php

namespace App\Application\Api\v1\Auth\Controllers;

use App\Application\Api\v1\Auth\Requests\AuthRequest;
use App\Application\Api\v1\Auth\Resources\ErrorLoginResource;
use App\Application\Api\v1\Auth\Resources\LoginResource;
use Application\Core\Http\Controllers\Controller;
use Domain\Auth\Actions\LoginAction;
use Domain\Auth\Actions\LogoutAction;
use Illuminate\Http\JsonResponse;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

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
     * @throws UnknownProperties
     * @throws Throwable
     */
    public function login(AuthRequest $authRequest, LoginAction $loginAction): JsonResponse|LoginResource
    {
        try
        {
            $tenantId = explode('.', $authRequest->getHost())[0];
            $response = $loginAction($authRequest->authData(), $tenantId);

            if (is_array($response) && array_key_exists('error',$response))
                return (new ErrorLoginResource($response))->response()->setStatusCode($response["status"]);
            else
                return new LoginResource($response);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }

    }

    public function logout(LogoutAction $logoutAction)
    {
        return $logoutAction();
    }

    /**
     * Verify if current token is valid
     */
}
