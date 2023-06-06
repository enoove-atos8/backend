<?php

namespace Application\Core\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="00.00.031",
 *      title="Atos242 API Documentation",
 *      description="API documentation for multi-tenant backend application of ecclesiastical management platform Atos242",
 *      @OA\Contact(
 *          email="developer@atos242.com"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_DYNAMIC_SERVER_NAME,
 *      description="Atos242 API Server"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="bearerAuth",
 *    )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
