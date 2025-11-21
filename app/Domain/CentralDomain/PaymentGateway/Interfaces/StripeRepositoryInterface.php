<?php

namespace Domain\CentralDomain\PaymentGateway\Interfaces;

interface StripeRepositoryInterface
{
    /**
     * Get subscription details from Stripe
     *
     * @param string $subscriptionId
     * @return array|null
     */
    public function getSubscriptionDetails(string $subscriptionId): ?array;

    /**
     * Get payment method details from Stripe
     *
     * @param string $paymentMethodId
     * @return array|null
     */
    public function getPaymentMethod(string $paymentMethodId): ?array;

    /**
     * Create a new subscription in Stripe
     *
     * @param string $customerId
     * @param string $priceId
     * @param array $options
     * @return array|null
     */
    public function createSubscription(string $customerId, string $priceId, array $options = []): ?array;

    /**
     * Cancel a subscription in Stripe
     *
     * @param string $subscriptionId
     * @return bool
     */
    public function cancelSubscription(string $subscriptionId): bool;

    /**
     * Create a new customer in Stripe
     *
     * @param array $customerData
     * @return array|null
     */
    public function createCustomer(array $customerData): ?array;
}
