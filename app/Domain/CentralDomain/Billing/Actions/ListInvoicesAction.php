<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Illuminate\Support\Collection;

class ListInvoicesAction
{
    public function __construct(
        private StripeRepositoryInterface $stripeRepository
    ) {}

    public function execute(string $stripeCustomerId): Collection
    {
        return $this->stripeRepository->listInvoices($stripeCustomerId);
    }
}
