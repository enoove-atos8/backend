<?php

declare(strict_types=1);

use Application\Api\v1\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Users Routes
|--------------------------------------------------------------------------
|
| Resource Group: general
| Resource: Users
| EndPoints: /v1/users
|
*/

Route::prefix('users')->group(function () {

    Route::put('/change-password', [UserController::class, 'changePassword']);
    Route::post('/files/assets/avatar', [UserController::class, 'uploadUserAvatar']);
    Route::get('/', [UserController::class, 'getUsers']);
    Route::get('/{id}', [UserController::class, 'getUserById']);
    Route::post('/', [UserController::class, 'createUser']);
    Route::put('/{id}/status', [UserController::class, 'updateStatus']);
    Route::put('/{id}', [UserController::class, 'updateUser']);
    Route::delete('/{id}', [UserController::class, 'deleteUser']);
});
