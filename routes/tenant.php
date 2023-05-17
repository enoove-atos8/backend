<?php

declare(strict_types=1);

use Application\Api\Auth\Controllers\AuthController;
use Application\Api\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
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
| Authentication Routes
|--------------------------------------------------------------------------
*/
    Route::post('login', [AuthController::class, 'login'])->name('login');

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */
        Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {
            Route::post('logout', 'logout');
        });

        Route::middleware('auth:sanctum')->controller(UserController::class)->group(function () {

            Route::get('/users', 'getUsers')->name('users.all');

        });

});
