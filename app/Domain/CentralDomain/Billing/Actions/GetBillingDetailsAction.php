<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use Domain\CentralDomain\Billing\DataTransferObjects\BillingDetailsData;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GetBillingDetailsAction
{
    public function __construct(
        private ChurchRepositoryInterface $churchRepository,
        private GetChurchPlanAction $getChurchPlanAction,
        private GetStripeSubscriptionAction $getStripeSubscriptionAction
    ) {}

    /**
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function execute(int $churchId): BillingDetailsData
    {
        $church = $this->churchRepository->getChurchById($churchId);
        $plan = $this->getChurchPlanAction->execute($churchId);
        $subscription = $this->getStripeSubscriptionAction->execute($churchId);

        return new BillingDetailsData(
            church: $church,
            plan: $plan,
            subscription: $subscription
        );
    }
}
