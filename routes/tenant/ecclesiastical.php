<?php

declare(strict_types=1);

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
        Route::post('/', [GroupController::class, 'createGroup']);

        Route::prefix('details')->group(function () {

            Route::prefix('movements')->group(function () {
                Route::get('/exportMovementsGroupData', [GroupController::class, 'exportMovementsGroupData']);
            });

            Route::prefix('teams')->group(function () {
                Route::get('/getMembersByGroupId', [MemberController::class, 'getMembersByGroupId']);
                Route::post('/addMembersToGroup', [GroupController::class, 'addMembersToGroup']);
            });
        });
    });
});
