<?php

declare(strict_types=1);

use App\Application\Api\v1\Auth\Controllers\AuthController;
use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Controllers\GroupController;
use App\Application\Api\v1\Financial\Reviewer\Controllers\FinancialReviewerController;
use App\Application\Api\v1\Notifications\Controllers\User\UserNotificationController;
use Application\Api\v1\Commons\Navigation\Controllers\NavigationMenuController;
use Application\Api\v1\Commons\Utils\Files\Upload\FileUploadController;
use Application\Api\v1\Ecclesiastical\Divisions\Controllers\DivisionsController;
use Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers\AccountController;
use Application\Api\v1\Financial\AccountsAndCards\Cards\Controllers\CardController;
use Application\Api\v1\Financial\Entries\Consolidation\Controllers\ConsolidationController;
use Application\Api\v1\Financial\Entries\Cults\Controllers\CultController;
use Application\Api\v1\Financial\Entries\Entries\Controllers\Consolidated\EntriesConsolidatedController;
use Application\Api\v1\Financial\Entries\Entries\Controllers\DuplicityAnalisys\DuplicityAnalisysController;
use Application\Api\v1\Financial\Entries\Entries\Controllers\General\EntriesController;
use Application\Api\v1\Financial\Entries\Entries\Controllers\Indicators\EntryIndicatorsController;
use Application\Api\v1\Financial\Entries\Reports\Controllers\MonthlyReportsController;
use Application\Api\v1\Financial\Exits\Exits\Controllers\ExitsController;
use Application\Api\v1\Financial\Exits\Payments\Controllers\PaymentsController;
use Application\Api\v1\Financial\Exits\Purchases\Controllers\InstallmentsController;
use Application\Api\v1\Financial\Exits\Purchases\Controllers\InvoiceController;
use Application\Api\v1\Financial\Movements\Controllers\MovementController;
use Application\Api\v1\Financial\ReceiptProcessing\Controllers\ReceiptProcessingController;
use Application\Api\v1\Mobile\SyncStorage\Controllers\SyncStorageController;
use Application\Api\v1\Secretary\Membership\Membership\Controllers\MemberController;
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

    Route::post('loginFromApp', [AuthController::class, 'loginFromApp'])->name('loginFromApp');


    /*
        |==========================================================================
        |=============================== Mobile Routes ============================
        |==========================================================================
        */

    Route::prefix('mobile')->group(function () {

        /*
        |------------------------------------------------------------------------------------------
        | Resource Group: ecclesiastical
        | Resource: Divisions
        | EndPoints: /v1/ecclesiastical/divisions
        |
        |   1 - GET - / - OK
        |------------------------------------------------------------------------------------------
        */

        Route::prefix('syncStorage')->group(function () {

            /*
             * Action: POST
             * EndPoint: /sendData
             * Description: send
             */

            Route::post('sendData', [SyncStorageController::class, 'sendDataToServer']);

        });

        Route::prefix('groups')->group(function () {

            /*
             * Action: POST
             * EndPoint: /sendData
             * Description: send
             */

            Route::get('/getGroupsByDivision', [GroupController::class, 'getGroupsToMobileApp']);

        });

        Route::prefix('payments')->group(function () {

            Route::prefix('categories')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get All exits by Date Range
                 */

                Route::get('/', [PaymentsController::class, 'getPaymentsCategories']);

            });

            Route::prefix('items')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get All exits by Date Range
                 */

                Route::get('/{id}', [PaymentsController::class, 'getPaymentItems']);
            });
        });

        Route::prefix('purchases')->group(function () {

            Route::prefix('cards')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get all cards
                 */

                Route::get('/', [CardController::class, 'getCards']);

            });

            Route::prefix('invoices')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get invoices by card id
                 */

                Route::get('/getInvoicesByCardId', [InvoiceController::class, 'getInvoicesByCardId']);

            });
        });

        Route::prefix('accounts')->group(function () {

            /*
            * Action: GET
            * Description: Get accounts
            */

            Route::get('getAccounts', [AccountController::class, 'getAccounts']);


            /*
             * Action: POST
             * Description: Save a new Account
             */

            Route::post('saveAccount', [AccountController::class, 'saveAccount']);


            /*
             * Action: DELETE
             * Description: Deactivate an Account
             */

            Route::delete('deactivateAccount/{id}', [AccountController::class, 'deactivateAccount']);

        });
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
        | Navigate menu Routes
        |--------------------------------------------------------------------------
        */

        Route::prefix('navigation')->group(function () {
            Route::get('/menu', [NavigationMenuController::class, 'getMenu']);
        });


        /*
        |--------------------------------------------------------------------------
        | Group Financial routes
        |--------------------------------------------------------------------------
        |
        */

        Route::prefix('utils')->group(function () {
            Route::prefix('files')->group(function () {
                Route::prefix('upload')->group(function () {
                    Route::post('/fileUpload', [FileUploadController::class, 'fileUpload']);
                });
            });
        });


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
            |   1 - GET - / - OK
            |   2 - GET - /getAmountByEntryType - OK
            |   3 - GET - /{id} - OK
            |   4 - GET - /getConsolidationEntries - OK
            |   5 - GET - /updateStatusConsolidationEntries - OK
            |   6 - POST - / - OK
            |   7 - PUT - /{id} - OK
            |   8 - DELETE - /{id} - OK
            |   9 - POST - /files/uploadReceiptEntry - OK
            |   10 - GET - /getEntriesByTransactionCompensation - OK
            |   11 - GET - /getTotalGeneralEntries - OK
            |------------------------------------------------------------------------------------------
            */

            Route::prefix('entries')->group(function () {

                /*
                |--------------------------------------------------------------------------
                | Group Entries Financial routes
                |--------------------------------------------------------------------------
                |
                */
                Route::prefix('entries')->group(function () {

                    /*
                     * Action: GET
                     * EndPoint: /
                     * Description: Get All Entries by Date Range
                     */

                    Route::get('/', [EntriesController::class, 'getEntriesByMonthlyRange']);


                    /*
                     * Action: GET
                     * EndPoint: /getAmountByEntryType/
                     * Description: Get amount of entries by Date Range
                    */
                    Route::get('/getAmountByEntryType/', [EntriesController::class, 'getAmountByEntryType']);



                    /*
                     * Action: GET
                     * EndPoint: /{id}
                     * Description: Get an entry by id
                     */

                    Route::get('/{id}', [EntriesController::class, 'getEntryById'])->whereNumber('id');


                    /*
                     * Action: POST
                     * EndPoint: /
                     * Description: Get All Entries by Date Range
                     */
                    Route::post('/', [EntriesController::class, 'createEntry']);


                    /*
                     * Action: PUT
                     * EndPoint: /{id}
                     * Description: Update an entry
                     */

                    Route::put('/{id}', [EntriesController::class, 'updateEntry'])->whereNumber('id');


                    /*
                     * Action: DELETE
                     * EndPoint: /{id}
                     * Description: Delete an entry
                     */

                    Route::delete('/{id}', [EntriesController::class, 'deleteEntry'])->whereNumber('id');


                    /*
                     * Action: POST
                     * EndPoint: /files/assets/avatar
                     * Description: Upload a receipt entry
                     */

                    Route::post('/files/assets/uploadReceiptEntry', [EntriesController::class, 'uploadEntryReceipt']);


                    /*
                     * Action: GET
                     * EndPoint: /getEntriesByTransactionCompensation
                     * Description: Get a  entries to compensate list
                     */

                    Route::get('/getEntriesByTransactionCompensation', [EntriesController::class, 'getEntriesByTransactionCompensation']);


                    /*
                     * Action: GET
                     * EndPoint: /getDevolutionEntries
                     * Description: Get a list of devolution entries
                     */

                    Route::get('/getDevolutionEntries', [EntriesController::class, 'getDevolutionEntries']);



                    /*
                     * Action: GET
                     * EndPoint: /getEntriesIndicators
                     * Description: Get entries indicators
                     */

                    Route::get('/getEntriesIndicators', [EntryIndicatorsController::class, 'getEntriesIndicators']);

                });


                /*
                |--------------------------------------------------------------------------
                | Cults Financial routes
                |--------------------------------------------------------------------------
                |
                */
                Route::prefix('cults')->group(function () {

                    /*
                     * Action: GET
                     * EndPoint: /getCults
                     * Description: Get all cults
                     */

                    Route::get('/getCults', [CultController::class, 'getCults']);



                    /*
                     * Action: GET
                     * EndPoint: /getCultById
                     * Description: Get a cult
                     */

                    Route::get('/getCultById', [CultController::class, 'getCultById']);



                    /*
                     * Action: POST
                     * EndPoint: /createCult
                     * Description: Create a new cult
                     */

                    Route::post('/', [CultController::class, 'saveCult']);



                    /*
                     * Action: PUT
                     * EndPoint: /updateCult
                     * Description: Update a cult
                     */

                    Route::put('/{id}', [CultController::class, 'saveCult'])->whereNumber('id');

                });




                /*
                |--------------------------------------------------------------------------
                | Consolidation Financial routes
                |--------------------------------------------------------------------------
                |
                */
                Route::prefix('consolidation')->group(function () {

                    /*
                     * Action: GET
                     * EndPoint: /getMonths
                     * Description: Get all consolidation months
                     */

                    Route::get('/getMonths', [ConsolidationController::class, 'getMonths']);



                    /*
                     * Action: POST
                     * EndPoint: /consolidateMonth
                     * Description: Consolidate month
                     */

                    Route::put('/consolidateMonth', [ConsolidationController::class, 'consolidateMonth']);




                    /*
                     * Action: POST
                     * EndPoint: /reopenMonth
                     * Description: Create a new cult
                     */

                    Route::put('/reopenConsolidatedMonth', [ConsolidationController::class, 'reopenMonth']);



                    /*
                     * Action: GET
                     * EndPoint: /getTotalAmountEntries
                     * Description: Get all entries amount for a month
                     */

                    Route::get('/getTotalAmountEntries', [ConsolidationController::class, 'getTotalAmountEntries']);

                });



                /*
                |--------------------------------------------------------------------------
                | Reports Financial routes
                |--------------------------------------------------------------------------
                |
                */
                Route::prefix('reports')->group(function () {

                    /*
                     * Action: POST
                     * EndPoint: /generateMonthlyReceiptsReport
                     * Description: Include report to be process by job schedule
                     */

                    Route::post('/generateMonthlyReceiptsReport', [MonthlyReportsController::class, 'generateMonthlyReceiptsReport']);


                    /*
                     * Action: POST
                     * EndPoint: /generateMonthlyEntriesReport
                     * Description: Include report to be process by job schedule
                     */

                    Route::post('/generateMonthlyEntriesReport', [MonthlyReportsController::class, 'generateMonthlyEntriesReport']);



                    /*
                     * Action: POST
                     * EndPoint: /generateReport
                     * Description: Include report to be process by job schedule
                     */

                    Route::get('/getReports', [MonthlyReportsController::class, 'getReports']);

                });



                /*
                |--------------------------------------------------------------------------
                | Receipts routes
                |--------------------------------------------------------------------------
                |
                */
                Route::prefix('receipts')->group(function () {


                    /*
                    |--------------------------------------------------------------------------
                    | Overview receipts routes
                    |--------------------------------------------------------------------------
                    |
                    */
                    Route::prefix('overview')->group(function () {

                    });



                    /*
                    |--------------------------------------------------------------------------
                    | Processed receipts routes
                    |--------------------------------------------------------------------------
                    |
                    */
                    Route::prefix('processed')->group(function () {


                    });


                    /*
                    |--------------------------------------------------------------------------
                    | Not processed receipts routes
                    |--------------------------------------------------------------------------
                    |
                    */
                    Route::prefix('not-processed')->group(function () {

                         /*
                         * Action: DELETE
                         * EndPoint: /
                         */

                        Route::delete('deleteReceiptsProcessing/{id}', [ReceiptProcessingController::class, 'deleteReceiptsProcessing'])->whereNumber('id');

                        /*
                        * Action: GET
                        * EndPoint: /
                        */

                        Route::get('getReceiptsProcessing/', [ReceiptProcessingController::class, 'getReceiptsProcessing']);

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | Duplicities analysis receipts routes
                    |--------------------------------------------------------------------------
                    |
                    */

                    Route::prefix('duplicities-analysis')->group(function () {

                        /*
                         * Action: GET
                         * EndPoint: /getDuplicitiesEntries
                         * Description: Get duplicities entries
                         */

                        Route::get('/getDuplicitiesEntries', [DuplicityAnalisysController::class, 'getDuplicitiesEntries']);



                        /*
                         * Action: GET
                         * EndPoint: /getReceiptsByEntriesIdsAction
                         * Description: Get receipts entries by id
                         */

                        Route::get('/getReceiptsByEntriesIds', [DuplicityAnalisysController::class, 'getReceiptsByEntriesIds']);



                        /*
                         * Action: GET
                         * EndPoint: /getReceiptsByEntriesIdsAction
                         * Description: Get receipts entries by id
                         */

                        Route::post('/saveDuplicityAnalysis', [DuplicityAnalisysController::class, 'saveDuplicityAnalysis']);

                    });
                });

            });



            /*
            |------------------------------------------------------------------------------------------
            | Resource Group: financial
            | Resource: Exits
            | EndPoints: /v1/financial/exits
            |
            |   1 - GET - / - OK
            |   2 - GET - /getAmountByEntryType - OK
            |------------------------------------------------------------------------------------------
            */

            Route::prefix('exits')->group(function () {

                /*
                |--------------------------------------------------------------------------
                | Group Exits Financial routes
                |--------------------------------------------------------------------------
                |
                */
                Route::prefix('exits')->group(function () {

                    /*
                     * Action: GET
                     * EndPoint: /
                     * Description: Get All exits by Date Range
                     */

                    Route::get('/', [ExitsController::class, 'getExits']);


                    /*
                     * Action: GET
                     * EndPoint: /getAmountByEntryType/
                     * Description: Get amount of entries by Date Range
                    */
                    Route::get('/getAmountByExitType/', [ExitsController::class, 'getAmountByExitType']);



                    /*
                     * Action: GET
                     * EndPoint: /{id}
                     * Description: Get an entry by id
                     */

                    //Route::get('/{id}', [EntriesController::class, 'getEntryById'])->whereNumber('id');


                    /*
                     * Action: POST
                     * EndPoint: /
                     */
                    Route::post('/', [ExitsController::class, 'createExit']);


                    /*
                     * Action: PUT
                     * EndPoint: /{id}
                     * Description: Update an entry
                     */

                    //Route::put('/{id}', [EntriesController::class, 'updateEntry'])->whereNumber('id');


                    /*
                     * Action: DELETE
                     * EndPoint: /{id}
                     * Description: Delete an exit
                     */

                    Route::delete('/{id}', [ExitsController::class, 'deleteExit'])->whereNumber('id');


                    /*
                     * Action: POST
                     * EndPoint: /files/assets/avatar
                     * Description: Upload a receipt entry
                     */

                    //Route::post('/files/assets/uploadReceiptEntry', [EntriesController::class, 'uploadEntryReceipt']);


                    /*
                     * Action: GET
                     * EndPoint: /getEntriesByTransactionCompensation
                     * Description: Get a  entries to compensate list
                     */

                    //Route::get('/getEntriesByTransactionCompensation', [EntriesController::class, 'getEntriesByTransactionCompensation']);


                    /*
                     * Action: GET
                     * EndPoint: /getDevolutionEntries
                     * Description: Get a list of devolution entries
                     */

                    //Route::get('/getDevolutionEntries', [EntriesController::class, 'getDevolutionEntries']);

                    Route::prefix('indicators')->group(function () {

                        /*
                         * Action: GET
                         * EndPoint: /getExitsAmount
                         * Description: Get exits indicators
                         */

                        Route::get('/getExitsAmount', [ExitsController::class, 'getExitsAmount']);

                    });

                });


                /*
                |--------------------------------------------------------------------------
                | Group Payments Financial routes
                |--------------------------------------------------------------------------
                |
                */
                Route::prefix('payments')->group(function () {



                    Route::prefix('categories')->group(function () {

                        /*
                         * Action: GET
                         * EndPoint: /
                         * Description: Get All exits by Date Range
                         */

                        Route::get('/', [PaymentsController::class, 'getPaymentsCategories']);

                    });


                    Route::prefix('items')->group(function () {

                        /*
                         * Action: GET
                         * EndPoint: /
                         * Description: Get All exits by Date Range
                         */

                        Route::get('/{id}', [PaymentsController::class, 'getPaymentItems']);


                        /*
                         * Action: GET
                         * EndPoint: /
                         * Description: Get All exits by Date Range
                         */

                        Route::post('/', [PaymentsController::class, 'addPaymentItems']);


                        /*
                         * Action: GET
                         * EndPoint: /
                         * Description: Get All exits by Date Range
                         */

                        Route::delete('/{id}', [PaymentsController::class, 'deletePaymentItems']);

                    });



                });


                /*
                |--------------------------------------------------------------------------
                | Group Purchases Financial routes
                |--------------------------------------------------------------------------
                |
                */
                Route::prefix('purchases')->group(function () {



                    Route::prefix('cards')->group(function () {

                        /*
                         * Action: GET
                         * EndPoint: /
                         * Description: Get all cards
                         */

                        Route::get('/', [CardController::class, 'getCards']);

                    });


                    Route::prefix('invoices')->group(function () {

                        /*
                         * Action: GET
                         * EndPoint: /
                         * Description: Get invoices months by card id
                         */

                        Route::get('/getInvoices', [InvoiceController::class, 'getInvoicesByCardId']);



                        /*
                         * Action: GET
                         * EndPoint: /
                         * Description: Get invoice indicators
                         */

                        Route::get('/getInvoiceIndicators', [InvoiceController::class, 'getInvoiceIndicators']);

                    });


                    Route::prefix('installments')->group(function () {

                        /*
                         * Action: GET
                         * EndPoint: /
                         * Description: Get installments of invoice by card id and invoice
                         */

                        Route::get('/getInstallments', [InstallmentsController::class, 'getInstalments']);



                        /*
                         * Action: GET
                         * EndPoint: /
                         * Description: Get installments by purchase id
                         */

                        Route::get('/getInstallmentsByPurchase', [InstallmentsController::class, 'getInstallmentsByPurchase']);

                    });



                });
            });


            /*
            |------------------------------------------------------------------------------------------
            | Resource Group: financial
            | Resource: Reviewers
            | EndPoints: /v1/financial/reviewers
            |
            |   1 - GET - /reviewers - OK
            |------------------------------------------------------------------------------------------
            */

            Route::prefix('reviewers')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get All financials reviewers
                 */

                Route::get('/', [FinancialReviewerController::class, 'getFinancialReviewers']);

            });


            /*
            |--------------------------------------------------------------------------
            | Group Dashboard Financial routes
            |--------------------------------------------------------------------------
            |
            */

            Route::prefix('dashboards')->group(function () {


                /*
                |------------------------------------------------------------------------------------------
                | Resource Group: Dashboard financial entries
                | Resource: Dashboards
                | EndPoints: /v1/financial/dashboards/entries
                |------------------------------------------------------------------------------------------
                */
                Route::prefix('entries')->group(function () {


                    /*
                     * Action: GET
                     * EndPoint: /
                     * Description: Get All consolidated entries to mount dashboard entries evolution
                     */

                    Route::get('/getEntriesEvolution/', [EntriesConsolidatedController::class, 'getEntriesEvolution']);

                });

            });



            /*
            |--------------------------------------------------------------------------
            | Movements Financial routes
            |--------------------------------------------------------------------------
            |
            */
            Route::prefix('movements')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /{id}
                 * Description: Get groups by division
                 */

                Route::get('getMovementsByGroup', [MovementController::class, 'getMovementsByGroup']);


                /*
                 * Action: GET
                 * EndPoint: /{id}
                 * Description: Get groups by division
                 */

                Route::get('getIndicatorsByGroup', [MovementController::class, 'getMovementsIndicatorsByGroup']);


                /*
                 * Action: POST
                 * EndPoint: /{id}
                 * Description: Get groups by division
                 */

                Route::post('addInitialBalance', [MovementController::class, 'addInitialBalance']);



                /*
                 * Action: PUT
                 * EndPoint: /{id}
                 * Description: Reset movements
                 */

                Route::put('resetBalance', [MovementController::class, 'resetBalance']);

            });



            /*
            |--------------------------------------------------------------------------
            | Account and Purchases Financial routes
            |--------------------------------------------------------------------------
            |
            */
            Route::prefix('accounts-cards')->group(function () {



                Route::prefix('cards')->group(function () {

                    /*
                     * Action: DELETE
                     * EndPoint: /{id}
                     * Description: Delete card by id
                     */

                    Route::delete('deleteCard', [CardController::class, 'deleteCard']);


                    /*
                     * Action: GET
                     * EndPoint: /{id}
                     * Description: Get groups by division
                     */

                    Route::get('getCards', [CardController::class, 'getCards']);


                    /*
                     * Action: GET
                     * EndPoint: /{id}
                     * Description: Get groups by division
                     */

                    Route::get('getCardById', [CardController::class, 'getCardById']);


                    /*
                     * Action: POST
                     * Description: Get groups by division
                     */

                    Route::post('saveCard', [CardController::class, 'saveCard']);

                });


                Route::prefix('accounts')->group(function () {

                    /*
                    * Action: GET
                    * Description: Get accounts
                    */

                    Route::get('getAccounts', [AccountController::class, 'getAccounts']);


                    /*
                     * Action: POST
                     * Description: Save a new Account
                     */

                    Route::post('saveAccount', [AccountController::class, 'saveAccount']);


                    /*
                     * Action: DELETE
                     * Description: Deactivate an Account
                     */

                    Route::delete('deactivateAccount/{id}', [AccountController::class, 'deactivateAccount']);

                });
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
             * Action: POST
             * EndPoint: /files/assets/avatar
             * Description: Upload a avatar user image
             */

            Route::post('/files/assets/avatar', [UserController::class, 'uploadUserAvatar']);
        });




        /*
        |--------------------------------------------------------------------------
        | Secretary routes
        |--------------------------------------------------------------------------
        |
        */

        Route::prefix('secretary')->group(function () {

            Route::prefix('membership')->group(function () {

                Route::prefix('membership')->group(function () {

                    /*
                     * Action: GET
                     * EndPoint: /
                     * Description: Get All members
                     */

                    Route::get('/', [MemberController::class, 'getMembers']);



                    /*
                     * Action: GET
                     * EndPoint: /getMembersIndicators
                     * Description: Get members and congregates quantities
                     */

                    Route::get('/getMembersIndicators/', [MemberController::class, 'getMembersIndicators']);




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

                Route::prefix('birthdays')->group(function () {

                    /*
                     * Action: GET
                     * EndPoint: /getMembersByBornMonth
                     * Description: Get members birthdays
                     */

                    Route::get('/getMembersByBornMonth', [MemberController::class, 'getMembersByBornMonth']);



                    /*
                     * Action: POST
                     * EndPoint: /exportBirthdaysData
                     * Description: Export data
                     */

                    Route::get('/exportBirthdaysData', [MemberController::class, 'exportBirthdaysData']);

                });

                Route::prefix('tithers')->group(function () {

                    /*
                     * Action: GET
                     * EndPoint: /getTithersByMonth
                     * Description: Get tithers by month
                     */

                    Route::get('/getTithersByDate', [MemberController::class, 'getTithersByDate']);



                    /*
                     * Action: POST
                     * EndPoint: /exportTithersData
                     * Description: Export data
                     */

                    Route::get('/exportTithersData', [MemberController::class, 'exportTithersData']);

                });

            });

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
        | Group Ecclesiastical routes
        |--------------------------------------------------------------------------
        |
        */

        Route::prefix('ecclesiastical')->group(function () {

            /*
            |------------------------------------------------------------------------------------------
            | Resource Group: ecclesiastical
            | Resource: Divisions
            | EndPoints: /v1/ecclesiastical/divisions
            |
            |   1 - GET - / - OK
            |------------------------------------------------------------------------------------------
            */

            Route::prefix('divisions')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /{enabled}
                 * Description: Get divisions enabled
                 */

                Route::get('getDivisions', [DivisionsController::class, 'getDivisions']);


                /*
                 * Action: GET
                 * EndPoint: /{enabled}
                 * Description: Get division id by name
                 */

                Route::get('getDivisionIdByName', [DivisionsController::class, 'getDivisionIdByName']);


                /*
                 * Action: GET
                 * EndPoint: /{enabled}
                 * Description: Get division by name
                 */

                Route::get('getDivisionByName', [DivisionsController::class, 'getDivisionByName']);


                /*
                 * Action: POST
                 * EndPoint: /
                 * Description: Create a division
                 */

                Route::post('/', [DivisionsController::class, 'createDivision']);

            });


            /*
            |------------------------------------------------------------------------------------------
            | Resource Group: ecclesiastical
            | Resource: Groups
            | EndPoints: /v1/ecclesiastical/groups
            |
            |   1 - GET - /getGroupsByDivision - OK
            |   2 - GET - /getAllGroups - OK
            |   3 - GET - /getAllGroupsWithDivisions - OK
            |------------------------------------------------------------------------------------------
            */

            Route::prefix('groups')->group(function () {

                /*
                 * Action: GET
                 * EndPoint: /{id}
                 * Description: Get groups by division
                 */

                Route::get('getGroupsByDivision', [GroupController::class, 'getGroupsByDivision']);



                /*
                 * Action: GET
                 * EndPoint: /{id}
                 * Description: Get groups by division
                 */

                Route::get('getGroupById', [GroupController::class, 'getGroupById']);


                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get all groups
                 */

                Route::get('getAllGroups', [GroupController::class, 'getAllGroups']);



                /*
                 * Action: GET
                 * EndPoint: /
                 * Description: Get all groups with division
                 */

                Route::get('getAllGroupsWithDivisions', [GroupController::class, 'getAllGroupsWithDivisions']);



                /*
                 * Action: POST
                 * EndPoint: /
                 * Description: Create a group
                 */

                Route::post('/', [GroupController::class, 'createGroup']);


                /*
                |--------------------------------------------------------------------------
                | Details groups routes
                |--------------------------------------------------------------------------
                |
                */

                Route::prefix('details')->group(function () {




                });





            });

        });



        /*
        |==========================================================================
        |=============================== Mobile Routes ============================
        |==========================================================================
        */

        /*
        |--------------------------------------------------------------------------
        | Group Ecclesiastical routes
        |--------------------------------------------------------------------------
        |
        */

    });
});
