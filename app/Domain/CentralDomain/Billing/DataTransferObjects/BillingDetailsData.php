<?php

namespace Domain\CentralDomain\Billing\DataTransferObjects;

use Domain\CentralDomain\Plans\DataTransferObjects\PlanData;
use Spatie\DataTransferObject\DataTransferObject;

class BillingDetailsData extends DataTransferObject
{
    /** @var PlanData|null */
    public PlanData|null $plan;

    /** @var SubscriptionData|null */
    public SubscriptionData|null $subscription;
}
