<?php

namespace Domain\CentralDomain\PaymentGateway\Interfaces;

use Domain\CentralDomain\Billing\DataTransferObjects\BoletoPaymentData;
use Domain\CentralDomain\Billing\DataTransferObjects\InvoiceData;
use Domain\CentralDomain\Billing\DataTransferObjects\PixPaymentData;
use Illuminate\Support\Collection;

interface StripeRepositoryInterface
{
    /**
     * Get subscription details from Stripe
     */
    public function getSubscriptionDetails(string $subscriptionId): ?array;

    /**
     * Get payment method details from Stripe
     */
    public function getPaymentMethod(string $paymentMethodId): ?array;

    /**
     * Create a new subscription in Stripe
     */
    public function createSubscription(string $customerId, string $priceId, array $options = []): ?array;

    /**
     * Cancel a subscription in Stripe
     */
    public function cancelSubscription(string $subscriptionId): bool;

    /**
     * Create a new customer in Stripe
     */
    public function createCustomer(array $customerData): ?array;

    /**
     * Attach a payment method to a customer
     */
    public function attachPaymentMethod(string $paymentMethodId, string $customerId): bool;

    /**
     * Set default payment method for a customer
     */
    public function setDefaultPaymentMethod(string $customerId, string $paymentMethodId): bool;

    /**
     * List all payment methods for a customer
     */
    public function listPaymentMethods(string $customerId): array;

    /**
     * Detach a payment method from a customer
     */
    public function detachPaymentMethod(string $paymentMethodId): bool;

    /**
     * Get customer details from Stripe
     */
    public function getCustomer(string $customerId): ?array;

    /**
     * List all invoices for a customer
     */
    public function listInvoices(string $customerId, int $limit = 20): Collection;

    /**
     * Get a specific invoice by ID
     */
    public function getInvoice(string $invoiceId): ?InvoiceData;

    /**
     * Create a PaymentIntent for Boleto payment
     */
    public function createBoletoPaymentIntent(array $params): ?BoletoPaymentData;

    /**
     * Create a PaymentIntent for PIX payment
     */
    public function createPixPaymentIntent(array $params): ?PixPaymentData;
}
