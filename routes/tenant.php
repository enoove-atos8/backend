<?php

declare(strict_types=1);

use App\Application\Api\v1\Auth\Controllers\AuthController;
use Application\Api\v1\Entry\Controllers\EntryController;
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
        | Financial routes
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
            |   4 - POST - /entries - OK
            |   5 - PUT - /entries/{id} - OK
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
                 * Description: Get All Entries by Date Range
                */
                Route::get('/getAmountByEntryType/', [EntryController::class, 'getAmountByEntryType']);



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

                Route::put('/{id}', [EntryController::class, 'updateEntry']);


            });
        });



        /*
        |--------------------------------------------------------------------------
        | Users routes
        |--------------------------------------------------------------------------
        |
        */

        Route::prefix('general')->group(function () {

            /*
            |------------------------------------------------------------------------------------------
            | Resource Group: general
            | Resource: Users
            | EndPoints: /v1/general/users
            |
            |   1 - GET - /users -
            |   2 - GET - /users/{id} -
            |   3 - POST - /users -
            |   4 - GET - /users/getTotalUsersByRoles -
            |   5 - PUT - /users/{id}
            |   6 - PUT - /users/{id}/status
            |------------------------------------------------------------------------------------------
            */

            Route::prefix('users')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get All Entries by Date Range
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
                 * Description: Create a user
                 */

                Route::put('/{id}', [UserController::class, 'updateUser']);
            });
        });




        /*
        |--------------------------------------------------------------------------
        | Users routes
        |--------------------------------------------------------------------------
        |
        */

        Route::get('/users', 'getUsers')->name('users.all');
        Route::get('/users/{id}', 'getUserByID')->name('users.byID')->where('id', '[0-9]+');




        /*
        |--------------------------------------------------------------------------
        | Others routes
        |--------------------------------------------------------------------------
        |
        */
        Route::get('/monthlyBudget', function () {
            return [
                'titleCard'     =>  'Orçamento mensal',
                'totalValue'    =>  '101,12%',
                'variation'     =>  4,
                'chart'         =>  [
                    'dataLabels' =>  ['01 Jan', '01 Fev', '01 Mar', '01 Abr'],
                    'dataSeries' => [
                        'name'  => 'R$',
                        'data'  => [15521.12, 15519, 15522, 15521]
                    ]
                ]
            ];
        });

    });
});
