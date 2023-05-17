<?php

declare(strict_types=1);

use Application\Api\Auth\Controllers\AuthController;
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

Route::prefix('api')->middleware(['api', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class,])->group(function () {

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
    Route::post('login', [AuthController::class, 'login'])->name('login');


    Route::get('/users', function () {

        //dd(\Domain\Users\Models\User::all());
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

});
