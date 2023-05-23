<?php

declare(strict_types=1);

use Application\Api\Auth\Controllers\AuthController;
use Application\Api\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::prefix('api')->middleware(['api', InitializeTenancyBySubdomain::class, PreventAccessFromCentralDomains::class,])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | API infos Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/version', function () {
        return [
            'api_version'   =>  '00.00.023',
            'branch'        =>  'develop',
            'tenant'        =>  tenant('id')
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
    });

    /*
    |--------------------------------------------------------------------------
    | Finance routes
    |--------------------------------------------------------------------------
    |
    */

    Route::middleware('auth:sanctum')->controller(UserController::class)->group(function () {

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
    });

    /*
    |--------------------------------------------------------------------------
    | Users routes
    |--------------------------------------------------------------------------
    |
    */

    Route::middleware('auth:sanctum')->controller(UserController::class)->group(function () {

        Route::get('/users', 'getUsers')->name('users.all');
        Route::get('/users/{id}', 'getUserByID')->name('users.byID')->where('id', '[0-9]+');
        Route::post('/users', [UserController::class, 'store']);

    });

});
