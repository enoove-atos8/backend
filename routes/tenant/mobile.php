<?php

declare(strict_types=1);

use App\Application\Api\v1\Ecclesiastical\Groups\Groups\Controllers\GroupController;
use Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers\AccountController;
use Application\Api\v1\Financial\AccountsAndCards\Cards\Controllers\CardController;
use Application\Api\v1\Financial\Exits\Payments\Controllers\PaymentsController;
use Application\Api\v1\Financial\Exits\Purchases\Controllers\InvoiceController;
use Application\Api\v1\Mobile\SyncStorage\Controllers\SyncStorageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile Routes
|--------------------------------------------------------------------------
*/

Route::prefix('mobile')->middleware('auth:sanctum')->group(function () {

    Route::prefix('syncStorage')->group(function () {
        Route::post('sendData', [SyncStorageController::class, 'sendDataToServer']);
    });

    Route::prefix('groups')->group(function () {
        Route::get('/getGroupsByDivision', [GroupController::class, 'getGroupsToMobileApp']);
    });

    Route::prefix('payments')->group(function () {

        Route::prefix('categories')->group(function () {
            Route::get('/', [PaymentsController::class, 'getPaymentsCategories']);
        });

        Route::prefix('items')->group(function () {
            Route::get('/{id}', [PaymentsController::class, 'getPaymentItems']);
        });
    });

    Route::prefix('accounts')->group(function () {
        Route::get('getAccounts', [AccountController::class, 'getAccounts']);
    });

    Route::prefix('purchases')->group(function () {

        Route::prefix('cards')->group(function () {
            Route::get('/', [CardController::class, 'getCards']);
        });

        Route::prefix('invoices')->group(function () {
            Route::get('/getInvoicesByCardId', [InvoiceController::class, 'getInvoicesByCardId']);
        });
    });
});
