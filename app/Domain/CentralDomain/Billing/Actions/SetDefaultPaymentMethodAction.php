<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use App\Domain\CentralDomain\Billing\Constants\SubscriptionMessages;
use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class SetDefaultPaymentMethodAction
{
    public function __construct(
        private StripeRepositoryInterface $stripeRepository
    ) {}

    public function execute(string $stripeCustomerId, string $paymentMethodId): bool
    {
        $result = $this->stripeRepository->setDefaultPaymentMethod($stripeCustomerId, $paymentMethodId);

        if (! $result) {
            throw new GeneralExceptions(SubscriptionMessages::ERROR_SET_DEFAULT_PAYMENT_METHOD, 500);
        }

        return true;
    }
}
