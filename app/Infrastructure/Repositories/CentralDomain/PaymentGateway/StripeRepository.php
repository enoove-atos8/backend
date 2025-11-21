<?php

namespace Infrastructure\Repositories\CentralDomain\PaymentGateway;

use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Stripe\StripeClient;

class StripeRepository implements StripeRepositoryInterface
{
    private StripeClient $stripe;

    const SUBSCRIPTION_KEY = 'subscription';
    const PAYMENT_METHOD_KEY = 'payment_method';
    const ID_KEY = 'id';
    const STATUS_KEY = 'status';
    const CURRENT_PERIOD_END_KEY = 'current_period_end';
    const CURRENT_PERIOD_START_KEY = 'current_period_start';
    const TRIAL_END_KEY = 'trial_end';
    const DEFAULT_PAYMENT_METHOD_KEY = 'default_payment_method';
    const CUSTOMER_KEY = 'customer';
    const TYPE_KEY = 'type';
    const BRAND_KEY = 'brand';
    const LAST4_KEY = 'last4';
    const EXP_MONTH_KEY = 'exp_month';
    const EXP_YEAR_KEY = 'exp_year';
    const NAME_KEY = 'name';
    const EMAIL_KEY = 'email';
    const PHONE_KEY = 'phone';
    const METADATA_KEY = 'metadata';

    public function __construct()
    {
        $secretKey = config('cashier.secret') ?? env('STRIPE_SECRET');

        if (!$secretKey) {
            throw new \Exception('Stripe secret key not configured. Set STRIPE_SECRET in .env file.');
        }

        $this->stripe = new StripeClient($secretKey);
    }

    /**
     * Get subscription details from Stripe
     *
     * @param string $subscriptionId
     * @return array|null
     */
    public function getSubscriptionDetails(string $subscriptionId): ?array
    {
        try {
            $subscription = $this->stripe->subscriptions->retrieve($subscriptionId);

            return [
                self::ID_KEY => $subscription->id,
                self::STATUS_KEY => $subscription->status,
                self::CURRENT_PERIOD_END_KEY => $subscription->current_period_end,
                self::CURRENT_PERIOD_START_KEY => $subscription->current_period_start,
                self::TRIAL_END_KEY => $subscription->trial_end,
                self::DEFAULT_PAYMENT_METHOD_KEY => $subscription->default_payment_method,
                self::CUSTOMER_KEY => $subscription->customer,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get payment method details from Stripe
     *
     * @param string $paymentMethodId
     * @return array|null
     */
    public function getPaymentMethod(string $paymentMethodId): ?array
    {
        try {
            $paymentMethod = $this->stripe->paymentMethods->retrieve($paymentMethodId);

            return [
                self::TYPE_KEY => $paymentMethod->type,
                self::BRAND_KEY => $paymentMethod->card->brand ?? null,
                self::LAST4_KEY => $paymentMethod->card->last4 ?? null,
                self::EXP_MONTH_KEY => $paymentMethod->card->exp_month ?? null,
                self::EXP_YEAR_KEY => $paymentMethod->card->exp_year ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create a new subscription in Stripe
     *
     * @param string $customerId
     * @param string $priceId
     * @param array $options
     * @return array|null
     */
    public function createSubscription(string $customerId, string $priceId, array $options = []): ?array
    {
        try {
            $params = array_merge([
                self::CUSTOMER_KEY => $customerId,
                'items' => [
                    ['price' => $priceId],
                ],
            ], $options);

            $subscription = $this->stripe->subscriptions->create($params);

            return [
                self::ID_KEY => $subscription->id,
                self::STATUS_KEY => $subscription->status,
                self::CURRENT_PERIOD_END_KEY => $subscription->current_period_end,
                self::TRIAL_END_KEY => $subscription->trial_end,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Cancel a subscription in Stripe
     *
     * @param string $subscriptionId
     * @return bool
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        try {
            $this->stripe->subscriptions->cancel($subscriptionId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create a new customer in Stripe
     *
     * @param array $customerData
     * @return array|null
     */
    public function createCustomer(array $customerData): ?array
    {
        try {
            $customer = $this->stripe->customers->create($customerData);

            return [
                self::ID_KEY => $customer->id,
                self::NAME_KEY => $customer->name,
                self::EMAIL_KEY => $customer->email,
                self::PHONE_KEY => $customer->phone,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
