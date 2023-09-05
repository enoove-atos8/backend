<?php

declare(strict_types=1);

use App\Application\Api\v1\Auth\Controllers\AuthController;
use Application\Api\v1\Entry\Controllers\EntryController;
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
            | Resource: Entries
            | EndPoints: /v1/financial/entries
            |
            |   1   - GET    - /entries -
            |   2   - GET    - /entries/getAmountByEntryTypes - OK - TST
            |   3   - POST   - /entries - OK - TST
            |   4   - POST   - /entries/{id} - OK - TST
            |------------------------------------------------------------------------------------------
            */

            Route::prefix('entries')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get All Entries by Date Range
                 */

                Route::get('/', function () {
                    return [
                        [
                            'id'                            =>  1,
                            'entryType'                     =>  'tithe',
                            'transactionType'               =>  'cash',
                            'transactionCompensation'       =>  'compensated',
                            'dateTransactionCompensation'   =>  '2023-09-01',
                            'dateEntryRegister'             =>  '2023-09-01',
                            'amount'                        =>  123.5,
                            'recipient'                     =>  null,
                            'member'    =>  [
                                'memberId'      =>  1,
                                'memberName'    =>  'Rafael Henrique Melo de Souza',
                                'memberAvatar'  =>  'assets/images/avatars/female-01.jpg',
                            ],
                            'reviewer'    =>  [
                                'reviewerId'      =>  3,
                                'reviewerName'    =>  'Jaime Lopes Junior',
                                'reviewerAvatar'  =>  'assets/images/avatars/female-02.jpg',
                            ]
                        ],
                        [
                            'id'                            =>  2,
                            'entryType'                     =>  'offers',
                            'transactionType'               =>  'pix',
                            'transactionCompensation'       =>  'compensated',
                            'dateTransactionCompensation'   =>  '2023-09-01',
                            'dateEntryRegister'             =>  '2023-09-01',
                            'amount'                        =>  1752.5,
                            'recipient'                     =>  null,
                            'member'    =>  [
                                'memberId'      =>  2,
                                'memberName'    =>  'Cláudio de Souza Lins',
                                'memberAvatar'  =>  'assets/images/avatars/male-01.jpg',
                            ],
                            'reviewer'    =>  [
                                'reviewerId'      =>  3,
                                'reviewerName'    =>  'Jaime Lopes Junior',
                                'reviewerAvatar'  =>  'assets/images/avatars/female-02.jpg',
                            ]
                        ]
                    ];
                });


                /*
                 * Action: GET
                 * EndPoint: /getAmountByEntryType/
                 * Description: Get All Entries by Date Range
                */
                Route::get('/getAmountByEntryType/', function (string $type) {
                    return [
                        'type'            =>  'tithe',
                        'amount'          =>  '1366.25',
                        'monthlyRange'    =>  [
                            'startDate' =>  '2023-09-04',
                            'endDate'   =>  '2023-09-04'
                        ]
                    ];
                });


                /*
                 * Action: POST
                 * EndPoint: /
                 * Description: Get All Entries by Date Range
                 */
                Route::post('/', [EntryController::class, 'createEntry']);
            });



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




        /*
        |--------------------------------------------------------------------------
        | Users routes
        |--------------------------------------------------------------------------
        |
        */

        Route::get('/users', 'getUsers')->name('users.all');
        Route::get('/users/{id}', 'getUserByID')->name('users.byID')->where('id', '[0-9]+');

    });
});
