<?php

declare(strict_types=1);

use Application\Api\v1\Billing\Controllers\BillingController;
use Application\Api\v1\Billing\Controllers\InvoiceController as BillingInvoiceController;
use Application\Api\v1\Billing\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Billing Routes
|--------------------------------------------------------------------------
|
| Resource: Billing
| EndPoints: /v1/billing
|
*/

Route::prefix('billing')->group(function () {

    Route::get('/plans', [BillingController::class, 'getPlans']);
    Route::get('/subscription', [BillingController::class, 'getBillingDetails']);
    Route::get('/payment-methods', [PaymentMethodController::class, 'getPaymentMethods']);
    Route::post('/payment-methods', [PaymentMethodController::class, 'addPaymentMethod']);
    Route::put('/payment-methods/{paymentMethodId}/default', [PaymentMethodController::class, 'setDefaultPaymentMethod']);
    Route::delete('/payment-methods/{paymentMethodId}', [PaymentMethodController::class, 'deletePaymentMethod']);
    Route::get('/invoices', [BillingInvoiceController::class, 'getInvoices']);
    Route::post('/invoices/{invoiceId}/pay', [BillingInvoiceController::class, 'payInvoice']);
});
