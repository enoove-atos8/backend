<?php

namespace Application\Api\Auth\Controllers;

use Application\Api\Auth\Requests\AuthRequest;
use Application\Api\Auth\Resources\ErrorLoginResource;
use Application\Api\Auth\Resources\LoginResource;
use Domain\Auth\Actions\LoginAction;
use Domain\Auth\Actions\LogoutAction;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    /**
     * @throws UnknownProperties
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
