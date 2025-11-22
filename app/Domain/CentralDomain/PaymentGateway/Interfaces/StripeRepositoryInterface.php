<?php

namespace Domain\CentralDomain\PaymentGateway\Interfaces;

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
}
