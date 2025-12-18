<?php

declare(strict_types=1);

use App\Application\Api\v1\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('loginFromApp', [AuthController::class, 'loginFromApp'])->name('loginFromApp');

Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {
    Route::post('logout', 'logout');
});
