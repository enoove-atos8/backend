<?php

declare(strict_types=1);

use App\Application\Api\v1\AI\Search\Controllers\SearchController;
use App\Application\Api\v1\Onboarding\Controllers\OnboardingController;
use Application\Api\v1\Commons\Navigation\Controllers\NavigationMenuController;
use Application\Api\v1\Commons\Utils\Files\Upload\FileUploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Commons Routes (Navigation, Utils, AI, Onboarding)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Onboarding Routes (Non-authenticated prefix, authenticated inside)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('onboarding')->group(function () {
    Route::get('/status', [OnboardingController::class, 'getStatus']);
});

/*
|--------------------------------------------------------------------------
| Navigation Routes
|--------------------------------------------------------------------------
*/

Route::prefix('navigation')->group(function () {
    Route::get('/menu', [NavigationMenuController::class, 'getMenu']);
});

/*
|--------------------------------------------------------------------------
| AI Search Routes
|--------------------------------------------------------------------------
*/

Route::post('ai/search', [SearchController::class, 'search']);
Route::get('ai/search/recent', [SearchController::class, 'recent']);
Route::get('ai/search/suggestions', [SearchController::class, 'suggestions']);

/*
|--------------------------------------------------------------------------
| Utils Routes
|--------------------------------------------------------------------------
*/

Route::prefix('utils')->group(function () {

    Route::prefix('files')->group(function () {

        Route::prefix('upload')->group(function () {
            Route::post('/fileUpload', [FileUploadController::class, 'fileUpload']);
            Route::post('/uploadFile', [FileUploadController::class, 'uploadFile']);
        });
    });
});
