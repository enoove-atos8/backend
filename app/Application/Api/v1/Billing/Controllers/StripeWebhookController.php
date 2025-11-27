<?php

namespace Application\Api\v1\Billing\Controllers;

use App\Domain\CentralDomain\Billing\Constants\InvoiceStatus;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    const EVENT_PAYMENT_INTENT_SUCCEEDED = 'payment_intent.succeeded';

    const EVENT_PAYMENT_INTENT_FAILED = 'payment_intent.payment_failed';

    const EVENT_INVOICE_PAID = 'invoice.paid';

    const EVENT_INVOICE_PAYMENT_FAILED = 'invoice.payment_failed';

    const METADATA_INVOICE_ID = 'invoice_id';

    const STATUS_KEY = 'status';

    const STATUS_SUCCESS = 'success';

    const STATUS_ERROR = 'error';

    const LOG_CHANNEL = 'stripe';

    const PAYMENT_INTENT_ID_KEY = 'payment_intent_id';

    const CUSTOMER_KEY = 'customer';

    const AMOUNT_KEY = 'amount';

    const AMOUNT_PAID_KEY = 'amount_paid';

    const AMOUNT_DUE_KEY = 'amount_due';

    const ERROR_KEY = 'error';

    const EVENT_TYPE_KEY = 'event_type';

    /**
     * Handle incoming Stripe webhook
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('cashier.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::channel(self::LOG_CHANNEL)->error('Webhook signature verification failed', [
                self::ERROR_KEY => $e->getMessage(),
            ]);

            return response()->json([self::STATUS_KEY => self::STATUS_ERROR], 400);
        }

        $this->processEvent($event);

        return response()->json([self::STATUS_KEY => self::STATUS_SUCCESS]);
    }

    /**
     * Process webhook event
     */
    private function processEvent(object $event): void
    {
        match ($event->type) {
            self::EVENT_PAYMENT_INTENT_SUCCEEDED => $this->handlePaymentIntentSucceeded($event->data->object),
            self::EVENT_PAYMENT_INTENT_FAILED => $this->handlePaymentIntentFailed($event->data->object),
            self::EVENT_INVOICE_PAID => $this->handleInvoicePaid($event->data->object),
            self::EVENT_INVOICE_PAYMENT_FAILED => $this->handleInvoicePaymentFailed($event->data->object),
            default => $this->logUnhandledEvent($event->type),
        };
    }

    /**
     * Handle payment_intent.succeeded event
     */
    private function handlePaymentIntentSucceeded(object $paymentIntent): void
    {
        $invoiceId = $paymentIntent->metadata->{self::METADATA_INVOICE_ID} ?? null;

        Log::channel(self::LOG_CHANNEL)->info('Payment intent succeeded', [
            self::PAYMENT_INTENT_ID_KEY => $paymentIntent->id,
            self::METADATA_INVOICE_ID => $invoiceId,
            self::AMOUNT_KEY => $paymentIntent->amount,
            self::STATUS_KEY => InvoiceStatus::PAID,
        ]);

        // TODO: Atualizar status da invoice no banco de dados
        // TODO: Liberar acesso do usuário se estava bloqueado
    }

    /**
     * Handle payment_intent.payment_failed event
     */
    private function handlePaymentIntentFailed(object $paymentIntent): void
    {
        $invoiceId = $paymentIntent->metadata->{self::METADATA_INVOICE_ID} ?? null;

        Log::channel(self::LOG_CHANNEL)->warning('Payment intent failed', [
            self::PAYMENT_INTENT_ID_KEY => $paymentIntent->id,
            self::METADATA_INVOICE_ID => $invoiceId,
            self::AMOUNT_KEY => $paymentIntent->amount,
            self::STATUS_KEY => InvoiceStatus::FAILED,
            self::ERROR_KEY => $paymentIntent->last_payment_error->message ?? null,
        ]);

        // TODO: Atualizar status da invoice para 'failed'
    }

    /**
     * Handle invoice.paid event
     */
    private function handleInvoicePaid(object $invoice): void
    {
        Log::channel(self::LOG_CHANNEL)->info('Invoice paid', [
            self::METADATA_INVOICE_ID => $invoice->id,
            self::CUSTOMER_KEY => $invoice->customer,
            self::AMOUNT_PAID_KEY => $invoice->amount_paid,
            self::STATUS_KEY => InvoiceStatus::PAID,
        ]);

        // TODO: Atualizar status da invoice no banco de dados
    }

    /**
     * Handle invoice.payment_failed event
     */
    private function handleInvoicePaymentFailed(object $invoice): void
    {
        Log::channel(self::LOG_CHANNEL)->warning('Invoice payment failed', [
            self::METADATA_INVOICE_ID => $invoice->id,
            self::CUSTOMER_KEY => $invoice->customer,
            self::AMOUNT_DUE_KEY => $invoice->amount_due,
            self::STATUS_KEY => InvoiceStatus::FAILED,
        ]);

        // TODO: Atualizar status da invoice para 'failed'
    }

    /**
     * Log unhandled webhook event
     */
    private function logUnhandledEvent(string $eventType): void
    {
        Log::channel(self::LOG_CHANNEL)->debug('Unhandled webhook event', [
            self::EVENT_TYPE_KEY => $eventType,
        ]);
    }
}
