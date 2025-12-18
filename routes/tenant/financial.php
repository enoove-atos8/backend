<?php

declare(strict_types=1);

use App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers\AccountFilesController;
use App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers\AccountMovementsController;
use App\Application\Api\v1\Financial\Reports\Balances\Controllers\MonthlyBalancesReportsController;
use App\Application\Api\v1\Financial\Reports\Entries\Controllers\MonthlyReportsController;
use App\Application\Api\v1\Financial\Reports\Exits\Controllers\MonthlyExitsReportsController;
use App\Application\Api\v1\Financial\Reports\Purchases\Controllers\MonthlyPurchasesReportsController;
use App\Application\Api\v1\Financial\Reviewer\Controllers\FinancialReviewerController;
use App\Application\Api\v1\Financial\Settings\Controllers\FinancialSettingsController;
use Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers\AccountController;
use Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers\AccountIndicatorsController;
use Application\Api\v1\Financial\AccountsAndCards\Cards\Controllers\CardController;
use Application\Api\v1\Financial\Entries\Consolidation\Controllers\ConsolidationController;
use Application\Api\v1\Financial\Entries\Cults\Controllers\CultController;
use Application\Api\v1\Financial\Entries\Entries\Controllers\Consolidated\EntriesConsolidatedController;
use Application\Api\v1\Financial\Entries\Entries\Controllers\DuplicityAnalisys\DuplicityAnalisysController;
use Application\Api\v1\Financial\Entries\Entries\Controllers\General\EntriesController;
use Application\Api\v1\Financial\Entries\Entries\Controllers\Indicators\EntryIndicatorsController;
use Application\Api\v1\Financial\Exits\Exits\Controllers\ExitsController;
use Application\Api\v1\Financial\Exits\Payments\Controllers\PaymentsController;
use Application\Api\v1\Financial\Exits\Purchases\Controllers\InstallmentsController;
use Application\Api\v1\Financial\Exits\Purchases\Controllers\InvoiceController;
use Application\Api\v1\Financial\Exits\Purchases\Controllers\PurchaseController;
use Application\Api\v1\Financial\Movements\Controllers\MovementController;
use Application\Api\v1\Financial\ReceiptProcessing\Controllers\ReceiptProcessingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Financial Routes
|--------------------------------------------------------------------------
|
| Resource Group: Financial
| EndPoints: /v1/financial
|
*/

Route::prefix('financial')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Entries Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('entries')->group(function () {

        Route::prefix('entries')->group(function () {

            Route::get('/', [EntriesController::class, 'getEntriesByMonthlyRange']);
            Route::get('/getAmountByEntryType/', [EntriesController::class, 'getAmountByEntryType']);
            Route::get('/{id}', [EntriesController::class, 'getEntryById'])->whereNumber('id');
            Route::post('/', [EntriesController::class, 'createEntry']);
            Route::put('/{id}', [EntriesController::class, 'updateEntry'])->whereNumber('id');
            Route::delete('/{id}', [EntriesController::class, 'deleteEntry'])->whereNumber('id');
            Route::post('/files/assets/uploadReceiptEntry', [EntriesController::class, 'uploadEntryReceipt']);
            Route::get('/getEntriesByTransactionCompensation', [EntriesController::class, 'getEntriesByTransactionCompensation']);
            Route::get('/getDevolutionEntries', [EntriesController::class, 'getDevolutionEntries']);
            Route::get('/getEntriesIndicators', [EntryIndicatorsController::class, 'getEntriesIndicators']);
        });

        Route::prefix('cults')->group(function () {

            Route::get('/getCults', [CultController::class, 'getCults']);
            Route::get('/getCultById', [CultController::class, 'getCultById']);
            Route::post('/', [CultController::class, 'saveCult']);
            Route::put('/{id}', [CultController::class, 'saveCult'])->whereNumber('id');
        });

        Route::prefix('consolidation')->group(function () {

            Route::get('/getMonths', [ConsolidationController::class, 'getMonths']);
            Route::put('/consolidateMonth', [ConsolidationController::class, 'consolidateMonth']);
            Route::put('/reopenConsolidatedMonth', [ConsolidationController::class, 'reopenMonth']);
            Route::get('/getTotalAmountEntries', [ConsolidationController::class, 'getTotalAmountEntries']);
        });

        Route::prefix('receipts')->group(function () {

            Route::prefix('overview')->group(function () {});
            Route::prefix('processed')->group(function () {});

            Route::prefix('not-processed')->group(function () {
                Route::delete('deleteReceiptsProcessing/{id}', [ReceiptProcessingController::class, 'deleteReceiptsProcessing'])->whereNumber('id');
                Route::get('getReceiptsProcessing/', [ReceiptProcessingController::class, 'getReceiptsProcessing']);
            });

            Route::prefix('duplicities-analysis')->group(function () {
                Route::get('/getDuplicitiesEntries', [DuplicityAnalisysController::class, 'getDuplicitiesEntries']);
                Route::get('/getReceiptsByEntriesIds', [DuplicityAnalisysController::class, 'getReceiptsByEntriesIds']);
                Route::post('/saveDuplicityAnalysis', [DuplicityAnalisysController::class, 'saveDuplicityAnalysis']);
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Exits Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('exits')->group(function () {

        Route::prefix('exits')->group(function () {

            Route::get('/', [ExitsController::class, 'getExits']);
            Route::get('/getAmountByExitType/', [ExitsController::class, 'getAmountByExitType']);
            Route::post('/', [ExitsController::class, 'createExit']);
            Route::delete('/{id}', [ExitsController::class, 'deleteExit'])->whereNumber('id');

            Route::prefix('indicators')->group(function () {
                Route::get('/getExitsAmount', [ExitsController::class, 'getExitsAmount']);
            });
        });

        Route::prefix('payments')->group(function () {

            Route::prefix('categories')->group(function () {
                Route::get('/', [PaymentsController::class, 'getPaymentsCategories']);
            });

            Route::prefix('items')->group(function () {
                Route::get('/{id}', [PaymentsController::class, 'getPaymentItems']);
                Route::post('/', [PaymentsController::class, 'addPaymentItems']);
                Route::delete('/{id}', [PaymentsController::class, 'deletePaymentItems']);
            });
        });

        Route::prefix('purchases')->group(function () {

            Route::delete('/{id}', [PurchaseController::class, 'deletePurchase'])->whereNumber('id');

            Route::prefix('cards')->group(function () {
                Route::get('/', [CardController::class, 'getCards']);
            });

            Route::prefix('invoices')->group(function () {
                Route::get('/getInvoices', [InvoiceController::class, 'getInvoicesByCardId']);
                Route::get('/getInvoiceIndicators', [InvoiceController::class, 'getInvoiceIndicators']);
            });

            Route::prefix('installments')->group(function () {
                Route::get('/getInstallments', [InstallmentsController::class, 'getInstalments']);
                Route::get('/getInstallmentsByPurchase', [InstallmentsController::class, 'getInstallmentsByPurchase']);
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Reports Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('reports')->group(function () {

        Route::prefix('entries')->group(function () {
            Route::post('/generateMonthlyReceiptsReport', [MonthlyReportsController::class, 'generateMonthlyReceiptsReport']);
            Route::post('/generateMonthlyEntriesReport', [MonthlyReportsController::class, 'generateMonthlyEntriesReport']);
            Route::get('/getReports', [MonthlyReportsController::class, 'getReports']);
        });

        Route::prefix('exits')->group(function () {
            Route::post('/generateMonthlyReceiptsReport', [MonthlyExitsReportsController::class, 'generateMonthlyExitsReceiptsReport']);
            Route::post('/generateMonthlyExitsReport', [MonthlyExitsReportsController::class, 'generateMonthlyExitsReport']);
            Route::get('/getReports', [MonthlyExitsReportsController::class, 'getReports']);
        });

        Route::prefix('balances')->group(function () {
            Route::post('/generateMonthlyBalancesReport', [MonthlyBalancesReportsController::class, 'generateMonthlyBalancesReport']);
            Route::get('/getReports', [MonthlyBalancesReportsController::class, 'getReports']);
        });

        Route::prefix('purchases')->group(function () {
            Route::post('/generateMonthlyReceiptsReport', [MonthlyPurchasesReportsController::class, 'generateMonthlyReceiptsPurchaseReport']);
            Route::post('/generateMonthlyPurchasesReport', [MonthlyPurchasesReportsController::class, 'generateMonthlyPurchasesReport']);
            Route::get('/getReports', [MonthlyPurchasesReportsController::class, 'getReports']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Reviewers Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('reviewers')->group(function () {

        Route::get('/', [FinancialReviewerController::class, 'getFinancialReviewers']);
        Route::post('/batch', [FinancialReviewerController::class, 'batchCreateReviewers']);
        Route::delete('/{id}', [FinancialReviewerController::class, 'deleteReviewer']);
    });

    /*
    |--------------------------------------------------------------------------
    | Settings Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('settings')->group(function () {

        Route::get('/', [FinancialSettingsController::class, 'getSettings']);
        Route::post('/', [FinancialSettingsController::class, 'saveSettings']);
    });

    /*
    |--------------------------------------------------------------------------
    | Dashboards Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('dashboards')->group(function () {

        Route::prefix('entries')->group(function () {
            Route::get('/getEntriesEvolution/', [EntriesConsolidatedController::class, 'getEntriesEvolution']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Movements Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('movements')->group(function () {

        Route::get('getMovementsByGroup', [MovementController::class, 'getMovementsByGroup']);
        Route::get('getIndicatorsByGroup', [MovementController::class, 'getMovementsIndicatorsByGroup']);
        Route::post('addInitialBalance', [MovementController::class, 'addInitialBalance']);
        Route::put('resetBalance', [MovementController::class, 'resetBalance']);
    });

    /*
    |--------------------------------------------------------------------------
    | Accounts and Cards Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('accounts-cards')->group(function () {

        Route::prefix('cards')->group(function () {

            Route::delete('deactivateCard', [CardController::class, 'deactivateCard']);
            Route::get('getCards', [CardController::class, 'getCards']);
            Route::get('getCardById', [CardController::class, 'getCardById']);
            Route::post('saveCard', [CardController::class, 'saveCard']);
            Route::delete('{id}', [CardController::class, 'deleteCard']);
        });

        Route::prefix('accounts')->group(function () {

            Route::get('getAccounts', [AccountController::class, 'getAccounts']);
            Route::post('saveAccount', [AccountController::class, 'saveAccount']);
            Route::delete('deactivateAccount/{id}', [AccountController::class, 'deactivateAccount']);
            Route::delete('{id}', [AccountController::class, 'deleteAccount']);

            Route::prefix('files')->group(function () {

                Route::post('saveFile', [AccountFilesController::class, 'saveFile']);
                Route::get('getAccountsFiles', [AccountFilesController::class, 'getAccountsFiles']);
                Route::post('processFile', [AccountFilesController::class, 'processFile']);
                Route::get('getPendingFiles', [AccountIndicatorsController::class, 'getPendingFiles']);
            });

            Route::prefix('movements')->group(function () {

                Route::get('getMovements', [AccountMovementsController::class, 'getMovements']);
                Route::get('getRecentMovements', [AccountIndicatorsController::class, 'getRecentMovements']);
            });

            Route::get('getAccountsIndicators', [AccountIndicatorsController::class, 'getAccountsIndicators']);
            Route::get('getMonthSummary', [AccountIndicatorsController::class, 'getMonthSummary']);
            Route::get('getConciliationStatus', [AccountIndicatorsController::class, 'getConciliationStatus']);
        });
    });
});
