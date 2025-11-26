<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use App\Domain\CentralDomain\Billing\Constants\SubscriptionMessages;
use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class AddPaymentMethodAction
{
    public function __construct(
        private StripeRepositoryInterface $stripeRepository
    ) {}

    public function execute(string $stripeCustomerId, string $paymentMethodId, bool $setAsDefault = false): array
    {
        $attached = $this->stripeRepository->attachPaymentMethod($paymentMethodId, $stripeCustomerId);

        if (! $attached) {
            throw new GeneralExceptions(SubscriptionMessages::ERROR_ATTACH_PAYMENT_METHOD, 500);
        }

        if ($setAsDefault) {
            $this->stripeRepository->setDefaultPaymentMethod($stripeCustomerId, $paymentMethodId);
        }

        $paymentMethod = $this->stripeRepository->getPaymentMethod($paymentMethodId);

        if (! $paymentMethod) {
            throw new GeneralExceptions(SubscriptionMessages::ERROR_PAYMENT_METHOD_NOT_FOUND, 404);
        }

        return array_merge($paymentMethod, [
            'id' => $paymentMethodId,
            'is_default' => $setAsDefault,
        ]);
    }
}
