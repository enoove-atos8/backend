<?php

declare(strict_types=1);

use App\Application\Api\v1\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


Route::prefix('api/v1')->middleware(['api', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class,])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | API infos Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/version', function () {
        return [
            'api_version'   =>  env('API_VERSION'),
            'branch'        =>  'develop',
            'tenant'        =>  tenant('id')
        ];
    });

//==================================================================================================================================
//================================================  NonAuthenticated Routes  =======================================================
//==================================================================================================================================

    /*
    |------------------------------------------------------------------------------------------
    | Resource: Login
    | EndPoints:
    |
    |   1   - GET    - /users/{id}/is-active - OK - TST
    |------------------------------------------------------------------------------------------
    */

        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {
            Route::post('logout', 'logout');
        });



//==================================================================================================================================
//================================================  Authenticated Routes  ==========================================================
//==================================================================================================================================



    Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::post('logout', 'logout');

        /*
        |--------------------------------------------------------------------------
        | Finance routes
        |--------------------------------------------------------------------------
        |
        */

        Route::get('/finance/monthlyBudget', function () {
            return [
                'titleCard'     =>  'Orçamento mensal',
                'totalValue'    =>  '101,12%',
                'variation'     =>  4,
                'chart'         =>  [
                    'dataLabels' =>  ['01 Jan', '01 Fev', '01 Mar', '01 Abr'],
                    'dataSeries' => [
                        'name'  => 'R$',
                        'data'  => [15521.12, 15519, 15522, 15521]
                    ]
                ]
            ];
        });


        /*
        |--------------------------------------------------------------------------
        | Users routes
        |--------------------------------------------------------------------------
        |
        */

        Route::get('/users', 'getUsers')->name('users.all');
        Route::get('/users/{id}', 'getUserByID')->name('users.byID')->where('id', '[0-9]+');

    });
});
