<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::prefix('api/v1')->middleware(['api', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | API Version Info
    |--------------------------------------------------------------------------
    */

    Route::get('/version', function () {
        return [
            'api_version' => env('API_VERSION'),
            'branch' => 'develop',
            'tenant' => tenant('id'),
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes (Non-authenticated)
    |--------------------------------------------------------------------------
    */

    require __DIR__.'/tenant/auth.php';

    /*
    |--------------------------------------------------------------------------
    | Onboarding Routes (Has its own auth middleware)
    |--------------------------------------------------------------------------
    */

    require __DIR__.'/tenant/commons.php';

    /*
    |--------------------------------------------------------------------------
    | Mobile Routes (Authenticated)
    |--------------------------------------------------------------------------
    */

    require __DIR__.'/tenant/mobile.php';

    /*
    |--------------------------------------------------------------------------
    | Web Authenticated Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Users Routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/tenant/users.php';

        /*
        |--------------------------------------------------------------------------
        | Financial Routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/tenant/financial.php';

        /*
        |--------------------------------------------------------------------------
        | Secretary Routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/tenant/secretary.php';

        /*
        |--------------------------------------------------------------------------
        | Ecclesiastical Routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/tenant/ecclesiastical.php';

        /*
        |--------------------------------------------------------------------------
        | Notifications Routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/tenant/notifications.php';

        /*
        |--------------------------------------------------------------------------
        | Billing Routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/tenant/billing.php';
    });
});
