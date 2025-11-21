<?php

namespace App\Domain\CentralDomain\PaymentGateway\Actions;

use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PaymentGateway\StripeRepository;

class GetStripeSubscriptionDetailsAction
{
    public function __construct(
        private StripeRepositoryInterface $stripeRepository
    ) {
    }

    /**
     * Get subscription details from Stripe including payment method
     *
     * @param string $subscriptionId
     * @return array|null
     * @throws GeneralExceptions
     */
    public function execute(string $subscriptionId): ?array
    {
        $subscriptionDetails = $this->stripeRepository->getSubscriptionDetails($subscriptionId);

        if (!$subscriptionDetails) {
            return null;
        }

        // Buscar dados do método de pagamento se existir
        $paymentMethod = null;
        if (isset($subscriptionDetails[StripeRepository::DEFAULT_PAYMENT_METHOD_KEY]) && $subscriptionDetails[StripeRepository::DEFAULT_PAYMENT_METHOD_KEY]) {
            $paymentMethod = $this->stripeRepository->getPaymentMethod($subscriptionDetails[StripeRepository::DEFAULT_PAYMENT_METHOD_KEY]);
        }

        return [
            StripeRepository::SUBSCRIPTION_KEY => $subscriptionDetails,
            StripeRepository::PAYMENT_METHOD_KEY => $paymentMethod,
        ];
    }
}
