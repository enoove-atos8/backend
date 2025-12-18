<?php

declare(strict_types=1);

use App\Application\Api\v1\Notifications\Controllers\User\UserNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
|
| Resource Group: Notifications
| EndPoints: /v1/notifications
|
*/

Route::prefix('notifications')->group(function () {

    Route::prefix('users')->group(function () {
        Route::post('/newUser', [UserNotificationController::class, 'newUser']);
    });
});
