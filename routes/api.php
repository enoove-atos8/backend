<?php

use Application\Api\v1\Church\Controllers\ChurchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | API infos Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/version', function () {
        return [
            'api_version'   =>  env('API_VERSION'),
            'branch'        =>  'develop',
            'tenant'        =>  'central'
        ];
    });

    /*
    |------------------------------------------------------------------------------------------
    | Resource: New Church
    | EndPoints:
    |
    |   1   - POST    - /church/new -
    |------------------------------------------------------------------------------------------
    */

    /*
    * EndPoint: /church/new
    * Description: Create a new church
    */


    Route::prefix('/church')->group(function (){

        Route::post('/', [ChurchController::class, 'createChurch']);

    });
});
