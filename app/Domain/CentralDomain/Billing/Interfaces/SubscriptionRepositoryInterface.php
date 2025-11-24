<?php

namespace Domain\CentralDomain\Billing\Interfaces;

use Domain\CentralDomain\Billing\DataTransferObjects\SubscriptionData;

interface SubscriptionRepositoryInterface
{
    public function getChurchSubscription(int $churchId): ?SubscriptionData;

    public function saveSubscription(int $churchId, array $stripeSubscription): array;
}
