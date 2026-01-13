<?php

declare(strict_types=1);

use App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Controllers\AmountRequestController;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Controllers\GroupController;
use Application\Api\v1\Ecclesiastical\Divisions\Controllers\DivisionsController;
use Application\Api\v1\Secretary\Membership\Membership\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ecclesiastical Routes
|--------------------------------------------------------------------------
|
| Resource Group: Ecclesiastical
| EndPoints: /v1/ecclesiastical
|
*/

Route::prefix('ecclesiastical')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Divisions Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('divisions')->group(function () {

        Route::get('getDivisions', [DivisionsController::class, 'getDivisions']);
        Route::get('getDivisionIdByName', [DivisionsController::class, 'getDivisionIdByName']);
        Route::get('getDivisionByName', [DivisionsController::class, 'getDivisionByName']);
        Route::post('/', [DivisionsController::class, 'createDivision']);
        Route::put('/{id}/status', [DivisionsController::class, 'updateStatus']);
        Route::put('/{id}/require-leader', [DivisionsController::class, 'updateRequireLeader']);
    });

    /*
    |--------------------------------------------------------------------------
    | Groups Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('groups')->group(function () {

        Route::get('getGroupsByDivision', [GroupController::class, 'getGroupsByDivision']);
        Route::get('getGroupById', [GroupController::class, 'getGroupById']);
        Route::get('getAllGroups', [GroupController::class, 'getAllGroups']);
        Route::get('getAllGroupsWithDivisions', [GroupController::class, 'getAllGroupsWithDivisions']);
        Route::get('getAllGroupsByAllDivisions', [GroupController::class, 'getAllGroupsByAllDivisions']);
        Route::post('/', [GroupController::class, 'createGroup']);
        Route::put('/{id}/leader', [GroupController::class, 'updateLeader']);
        Route::put('/{id}/status', [GroupController::class, 'updateStatus']);
        Route::delete('/{id}', [GroupController::class, 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | Group Settings Routes (Configurações do Grupo)
        |--------------------------------------------------------------------------
        */

        Route::prefix('{id}/settings')->whereNumber('id')->group(function () {
            Route::put('/ministerial-investment-limit', [GroupController::class, 'updateMinisterialInvestmentLimit']);
        });

        Route::prefix('details')->group(function () {

            Route::prefix('movements')->group(function () {
                Route::get('/exportMovementsGroupData', [GroupController::class, 'exportMovementsGroupData']);
            });

            Route::prefix('teams')->group(function () {
                Route::get('/getMembersByGroupId', [MemberController::class, 'getMembersByGroupId']);
                Route::post('/addMembersToGroup', [GroupController::class, 'addMembersToGroup']);
                Route::get('/exportGroupTithersData', [GroupController::class, 'exportGroupTithersData']);
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Amount Requests Routes (Solicitação de Verbas)
        |--------------------------------------------------------------------------
        */

        Route::prefix('amount-requests')->group(function () {

            Route::get('/', [AmountRequestController::class, 'getAmountRequests']);
            Route::post('/', [AmountRequestController::class, 'createAmountRequest']);
            Route::get('/indicators', [AmountRequestController::class, 'getAmountRequestIndicators']);
            Route::get('/{id}', [AmountRequestController::class, 'getAmountRequestById'])->whereNumber('id');
            Route::put('/{id}', [AmountRequestController::class, 'updateAmountRequest'])->whereNumber('id');
            Route::delete('/{id}', [AmountRequestController::class, 'deleteAmountRequest'])->whereNumber('id');

            Route::post('/{id}/approve', [AmountRequestController::class, 'approveAmountRequest'])->whereNumber('id');
            Route::post('/{id}/reject', [AmountRequestController::class, 'rejectAmountRequest'])->whereNumber('id');
            Route::put('/{id}/link-exit', [AmountRequestController::class, 'linkExitToAmountRequest'])->whereNumber('id');
            Route::post('/{id}/close', [AmountRequestController::class, 'closeAmountRequest'])->whereNumber('id');

            Route::get('/{id}/receipts', [AmountRequestController::class, 'getAmountRequestReceipts'])->whereNumber('id');
            Route::post('/{id}/receipts', [AmountRequestController::class, 'createAmountRequestReceipt'])->whereNumber('id');
            Route::put('/{id}/receipts/{receiptId}', [AmountRequestController::class, 'updateAmountRequestReceipt'])
                ->whereNumber('id')
                ->whereNumber('receiptId');
            Route::delete('/{id}/receipts/{receiptId}', [AmountRequestController::class, 'deleteAmountRequestReceipt'])
                ->whereNumber('id')
                ->whereNumber('receiptId');

            Route::get('/{id}/reminders', [AmountRequestController::class, 'getAmountRequestReminders'])->whereNumber('id');
            Route::post('/{id}/reminders', [AmountRequestController::class, 'createAmountRequestReminder'])->whereNumber('id');

            Route::get('/{id}/history', [AmountRequestController::class, 'getAmountRequestHistory'])->whereNumber('id');
        });
    });
});
