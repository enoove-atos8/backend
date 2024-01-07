<?php

namespace App\Application\Api\v1\Notifications\Controllers\User;

use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class UserNotificationController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     */
    public function newUser(Request $request): Response
    {

    }
}
