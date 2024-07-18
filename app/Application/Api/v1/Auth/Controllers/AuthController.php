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



    /**
     * @param LogoutAction $logoutAction
     * @return mixed
     */
    public function logout(LogoutAction $logoutAction)
    {
        return $logoutAction();
    }
}
