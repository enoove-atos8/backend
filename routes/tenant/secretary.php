<?php

declare(strict_types=1);

use Application\Api\v1\Secretary\Membership\Membership\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Secretary Routes
|--------------------------------------------------------------------------
|
| Resource Group: Secretary
| EndPoints: /v1/secretary
|
*/

Route::prefix('secretary')->group(function () {

    Route::prefix('membership')->group(function () {

        Route::prefix('membership')->group(function () {

            Route::get('/', [MemberController::class, 'getMembers']);
            Route::get('/getMembersIndicators/', [MemberController::class, 'getMembersIndicators']);
            Route::get('/{id}', [MemberController::class, 'getMemberById']);
            Route::post('/', [MemberController::class, 'createMember']);
            Route::post('/batch', [MemberController::class, 'batchCreateMembers']);
            Route::put('/{id}/status', [MemberController::class, 'updateStatus']);
            Route::put('/{id}/deactivation-reason', [MemberController::class, 'updateDeactivationReason']);
            Route::put('/{id}', [MemberController::class, 'updateMember']);
            Route::post('/files/assets/avatar', [MemberController::class, 'uploadMemberAvatar']);
        });

        Route::prefix('birthdays')->group(function () {
            Route::get('/getMembersByBornMonth', [MemberController::class, 'getMembersByBornMonth']);
            Route::get('/exportBirthdaysData', [MemberController::class, 'exportBirthdaysData']);
        });

        Route::prefix('tithers')->group(function () {
            Route::get('/getTithersByDate', [MemberController::class, 'getTithersByDate']);
            Route::get('/exportTithersData', [MemberController::class, 'exportTithersData']);
        });

        Route::prefix('teams')->group(function () {
            Route::get('/getMembersByGroupId', [MemberController::class, 'getMembersByGroupId']);
        });
    });
});
