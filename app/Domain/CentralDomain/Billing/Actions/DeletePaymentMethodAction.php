<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use App\Domain\CentralDomain\Billing\Constants\SubscriptionMessages;
use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeletePaymentMethodAction
{
    public function __construct(
        private StripeRepositoryInterface $stripeRepository
    ) {}

    public function execute(string $stripeCustomerId, string $paymentMethodId): bool
    {
        $customer = $this->stripeRepository->getCustomer($stripeCustomerId);

        if ($customer && $customer['default_payment_method'] === $paymentMethodId) {
            throw new GeneralExceptions(SubscriptionMessages::ERROR_CANNOT_DELETE_DEFAULT_PAYMENT_METHOD, 400);
        }

        $result = $this->stripeRepository->detachPaymentMethod($paymentMethodId);

        if (! $result) {
            throw new GeneralExceptions(SubscriptionMessages::ERROR_DETACH_PAYMENT_METHOD, 500);
        }

        return true;
    }
}
