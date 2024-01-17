<?php

declare(strict_types=1);

use App\Application\Api\v1\Auth\Controllers\AuthController;
use App\Application\Api\v1\Notifications\Controllers\User\UserNotificationController;
use Application\Api\v1\Entry\Controllers\EntryController;
use Application\Api\v1\Members\Controllers\MemberController;
use Application\Api\v1\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


Route::prefix('api/v1')->middleware(['api', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class,])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | API infos Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/version', function () {
        return [
            'api_version'   =>  env('API_VERSION'),
            'branch'        =>  'develop',
            'tenant'        =>  tenant('id')
        ];
    });

//==================================================================================================================================
//================================================  NonAuthenticated Routes  =======================================================
//==================================================================================================================================

    /*
    |------------------------------------------------------------------------------------------
    | Resource: Login
    | EndPoints:
    |
    |   1   - GET    - /users/{id}/is-active - OK - TST
    |------------------------------------------------------------------------------------------
    */

        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {
            Route::post('logout', 'logout');
        });



//==================================================================================================================================
//================================================  Authenticated Routes  ==========================================================
//==================================================================================================================================



    Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::post('logout', 'logout');



        /*
        |--------------------------------------------------------------------------
        | Group Financial routes
        |--------------------------------------------------------------------------
        |
        */

        Route::prefix('financial')->group(function () {

            /*
            |------------------------------------------------------------------------------------------
            | Resource Group: financial
            | Resource: Entries
            | EndPoints: /v1/financial/entries
            |
            |   1 - GET - /entries - OK
            |   2 - GET - /entries/getAmountByEntryType - OK
            |   3 - GET - /entries/{id} - OK
            |   4 - GET - /entries/getConsolidationEntries - OK
            |   5 - GET - /entries/updateStatusConsolidationEntries - OK
            |   6 - POST - /entries - OK
            |   7 - PUT - /entries/{id} - OK
            |------------------------------------------------------------------------------------------
            */

            Route::prefix('entries')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get All Entries by Date Range
                 */

                Route::get('/', [EntryController::class, 'getEntriesByMonthlyRange']);


                /*
                 * Action: GET
                 * EndPoint: /getAmountByEntryType/
                 * Description: Get amount of entries by Date Range
                */
                Route::get('/getAmountByEntryType/', [EntryController::class, 'getAmountByEntryType']);


                /*
                 * Action: GET
                 * EndPoint: /getConsolidationEntries
                 * Description: Get a list of months do not consolidated
                 */

                Route::get('/getConsolidationEntriesByStatus/', [EntryController::class, 'getConsolidationEntriesByStatus']);


                /*
                 * Action: PUT
                 * EndPoint: /updateStatusConsolidationEntries
                 * Description: Update status consolidation from month
                 */

                Route::put('/updateStatusConsolidationEntries', [EntryController::class, 'updateStatusConsolidationEntries']);


                /*
                 * Action: GET
                 * EndPoint: /{id}
                 * Description: Get an entry by id
                 */

                Route::get('/{id}', [EntryController::class, 'getEntryById']);


                /*
                 * Action: POST
                 * EndPoint: /
                 * Description: Get All Entries by Date Range
                 */
                Route::post('/', [EntryController::class, 'createEntry']);



                /*
                 * Action: PUT
                 * EndPoint: /{id}
                 * Description: Update an entry
                 */

                Route::put('/{id}', [EntryController::class, 'updateEntry'])->where('id', '[0-9]+');;

            });
        });


        /*
        |--------------------------------------------------------------------------
        | Users routes
        |------------------------------------------------------------------------------------------
        | Resource Group: general
        | Resource: Users
        | EndPoints: /v1/general/users
        |
        |   1 - GET - /users - OK
        |   2 - GET - /users/{id} - OK
        |   3 - POST - /users - OK
        |   4 - GET - /users/getTotalUsersByRoles -
        |   5 - PUT - /users/{id} - OK
        |   6 - PUT - /users/{id}/status - OK
        |   7 - POST - /files/assets/avatar - OK
        |------------------------------------------------------------------------------------------
        */

        Route::prefix('users')->group(function () {

            /*
             * Action: GET
             * EndPoint: /
             * Description: Get All users
             */

            Route::get('/', [UserController::class, 'getUsers']);


            /*
             * Action: GET
             * EndPoint: /{id}
             * Description: Get user by id
             */

            Route::get('/{id}', [UserController::class, 'getUserById']);


            /*
             * Action: POST
             * EndPoint: /
             * Description: Create a user
             */

            Route::post('/', [UserController::class, 'createUser']);


            /*
             * Action: PUT
             * EndPoint: /{id}/status
             * Description: Update status of activation user
             */

            Route::put('/{id}/status', [UserController::class, 'updateStatus']);


            /*
             * Action: PUT
             * EndPoint: /{id}
             * Description: Update a user
             */

            Route::put('/{id}', [UserController::class, 'updateUser']);



            /*
             * Action: PUT
             * EndPoint: /files/assets/avatar
             * Description: Upload a avatar user image
             */

            Route::post('/files/assets/avatar', [UserController::class, 'uploadUserAvatar']);
        });



        /*
        |--------------------------------------------------------------------------
        | Members routes
        |------------------------------------------------------------------------------------------
        | Resource Group: general
        | Resource: Members
        | EndPoints: /v1/general/members
        |
        |   1 - GET - /members - OK
        |   2 - GET - /members/{id} - OK
        |   3 - POST - /members - OK
        |   4 - PUT - /members/{id} - OK
        |   5 - PUT - /members/{id}/status - OK
        |   6 - POST - /files/assets/avatar - OK
        |------------------------------------------------------------------------------------------
        */

        Route::prefix('members')->group(function () {

            /*
             * Action: GET
             * EndPoint: /
             * Description: Get All members
             */

            Route::get('/', [MemberController::class, 'getMembers']);


            /*
             * Action: GET
             * EndPoint: /{id}
             * Description: Get member by id
             */

            Route::get('/{id}', [MemberController::class, 'getMemberById']);


            /*
             * Action: POST
             * EndPoint: /
             * Description: Create a member
             */

            Route::post('/', [MemberController::class, 'createMember']);


            /*
             * Action: PUT
             * EndPoint: /{id}/status
             * Description: Update status of activation member
             */

            Route::put('/{id}/status', [MemberController::class, 'updateStatus']);


            /*
             * Action: PUT
             * EndPoint: /{id}
             * Description: Update a member
             */

            Route::put('/{id}', [MemberController::class, 'updateMember']);



            /*
             * Action: PUT
             * EndPoint: /files/assets/avatar
             * Description: Upload a avatar member image
             */

            Route::post('/files/assets/avatar', [MemberController::class, 'uploadMemberAvatar']);
        });


        /*
        |------------------------------------------------------------------------------------------
        | Notification routes
        |------------------------------------------------------------------------------------------
        | Resource Group: Notifications
        | EndPoints: /v1/notification
        |
        |------------------------------------------------------------------------------------------
        */

        Route::prefix('notifications')->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Users Notifications
            |------------------------------------------------------------------------------------------
            | Resource: Users
            | EndPoints: /v1/notification/users
            |
            |   1 - POST - /newUser - OK
            |------------------------------------------------------------------------------------------
            */

            Route::prefix('users')->group(function () {

                Route::post('/newUser', [UserNotificationController::class, 'newUser']);

            });
            /*
             * Action: GET
             * EndPoint: /
             * Description: Get All users
             */


        });


        /*
        |--------------------------------------------------------------------------
        | Members routes
        |------------------------------------------------------------------------------------------
        | Resource Group: general
        | Resource: Members
        | EndPoints: /v1/general/members
        |
        |   1 - GET - /members - OK
        |   2 - GET - /members/{id} - OK
        |   3 - POST - /members - OK
        |   4 - PUT - /members/{id} - OK
        |   5 - PUT - /members/{id}/status - OK
        |   6 - POST - /files/assets/avatar - OK
        |------------------------------------------------------------------------------------------
        */

        Route::prefix('members')->group(function () {

            /*
             * Action: GET
             * EndPoint: /
             * Description: Get All members
             */

            Route::get('/', [MemberController::class, 'getMembers']);


            /*
             * Action: GET
             * EndPoint: /{id}
             * Description: Get member by id
             */

            Route::get('/{id}', [MemberController::class, 'getMemberById']);


            /*
             * Action: POST
             * EndPoint: /
             * Description: Create a member
             */

            Route::post('/', [MemberController::class, 'createMember']);


            /*
             * Action: PUT
             * EndPoint: /{id}/status
             * Description: Update status of activation member
             */

            Route::put('/{id}/status', [MemberController::class, 'updateStatus']);


            /*
             * Action: PUT
             * EndPoint: /{id}
             * Description: Update a member
             */

            Route::put('/{id}', [MemberController::class, 'updateMember']);



            /*
             * Action: PUT
             * EndPoint: /files/assets/avatar
             * Description: Upload a avatar member image
             */

            Route::post('/files/assets/avatar', [MemberController::class, 'uploadMemberAvatar']);
        });
    });
});
