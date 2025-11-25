<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use App\Domain\CentralDomain\Billing\Constants\SubscriptionMessages;
use App\Domain\CentralDomain\Churches\Church\Actions\UpdateChurchAction;
use Domain\CentralDomain\Billing\Interfaces\SubscriptionRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PaymentGateway\StripeRepository;

class SaveSubscriptionAction
{
    private const TRIAL_ENDS_AT = 'trial_ends_at';

    private const MEMBER_COUNT = 'member_count';

    private const QUANTITY = 'quantity';

    private const PM_TYPE = 'pm_type';

    private const PM_LAST_FOUR = 'pm_last_four';

    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private UpdateChurchAction $updateChurchAction
    ) {}

    /**
     * Save subscription data locally after Stripe creation
     *
     * @param  array  $subscriptionResult  Result from CreateSubscriptionAction
     *
     * @throws GeneralExceptions
     */
    public function execute(
        int $churchId,
        array $subscriptionResult,
        ?string $paymentMethodBrand = null,
        ?string $paymentMethodLast4 = null
    ): bool {
        try {
            $subscription = $subscriptionResult[StripeRepository::SUBSCRIPTION_KEY];
            $paymentMethod = $subscriptionResult[StripeRepository::PAYMENT_METHOD_KEY];

            $subscriptionData = $this->subscriptionRepository->saveSubscription($churchId, $subscription);

            $churchUpdateData = [
                self::TRIAL_ENDS_AT => $subscriptionData[self::TRIAL_ENDS_AT],
                self::MEMBER_COUNT => $subscriptionData[self::QUANTITY],
            ];

            if ($paymentMethod && isset($paymentMethod[StripeRepository::BRAND_KEY]) && isset($paymentMethod[StripeRepository::LAST4_KEY])) {
                $churchUpdateData[self::PM_TYPE] = $paymentMethod[StripeRepository::BRAND_KEY];
                $churchUpdateData[self::PM_LAST_FOUR] = $paymentMethod[StripeRepository::LAST4_KEY];
            }

            $this->updateChurchAction->execute($churchId, $churchUpdateData);

            return true;

        } catch (GeneralExceptions $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new GeneralExceptions(
                SubscriptionMessages::ERROR_SAVE_SUBSCRIPTION.': '.$e->getMessage(),
                500
            );
        }
    }
}
