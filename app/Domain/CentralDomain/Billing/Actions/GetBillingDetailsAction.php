<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use Domain\CentralDomain\Billing\DataTransferObjects\BillingDetailsData;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GetBillingDetailsAction
{
    public function __construct(
        private GetChurchPlanAction $getChurchPlanAction,
        private GetStripeSubscriptionAction $getStripeSubscriptionAction
    ) {
    }

    /**
     * @param int $churchId
     * @return BillingDetailsData
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function execute(int $churchId): BillingDetailsData
    {
        $plan = $this->getChurchPlanAction->execute($churchId);
        $subscription = $this->getStripeSubscriptionAction->execute($churchId);

        return new BillingDetailsData(
            plan: $plan,
            subscription: $subscription
        );
    }
}
