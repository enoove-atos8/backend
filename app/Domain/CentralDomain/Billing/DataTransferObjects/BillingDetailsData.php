<?php

namespace Domain\CentralDomain\Billing\DataTransferObjects;

use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
use Domain\CentralDomain\Plans\DataTransferObjects\PlanData;
use Spatie\DataTransferObject\DataTransferObject;

class BillingDetailsData extends DataTransferObject
{
    public ?ChurchData $church;

    public ?PlanData $plan;

    public ?SubscriptionData $subscription;
}
