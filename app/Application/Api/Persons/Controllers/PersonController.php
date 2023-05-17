<?php

namespace Application\Api\Persons\Controllers;

use Application\Api\Persons\Requests\PersonRequest;
use Application\Api\Persons\Resources\ErrorPersonResource;
use Application\Api\Persons\Resources\PersonResource;
use Application\Api\Persons\Resources\PersonResourceCollection;
use Domain\Users\Actions\CreateUserAction;
use Domain\Users\Actions\ListUserAction;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PersonController extends Controller
{

}
