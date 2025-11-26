<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;

class ListPaymentMethodsAction
{
    public function __construct(
        private StripeRepositoryInterface $stripeRepository
    ) {}

    public function execute(string $stripeCustomerId): array
    {
        return $this->stripeRepository->listPaymentMethods($stripeCustomerId);
    }
}
