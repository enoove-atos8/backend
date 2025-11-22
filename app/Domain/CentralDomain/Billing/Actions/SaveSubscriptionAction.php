<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use App\Domain\CentralDomain\Billing\Constants\SubscriptionMessages;
use Domain\CentralDomain\Billing\Interfaces\SubscriptionRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PaymentGateway\StripeRepository;

class SaveSubscriptionAction
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private ChurchRepositoryInterface $churchRepository
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

            // Preparar dados da subscription para salvar
            $subscriptionData = [
                'stripe_id' => $subscription[StripeRepository::ID_KEY],
                'stripe_status' => $subscription[StripeRepository::STATUS_KEY],
                'stripe_price' => null,
                'quantity' => 1,
                'trial_ends_at' => isset($subscription[StripeRepository::TRIAL_END_KEY])
                    ? date('Y-m-d H:i:s', $subscription[StripeRepository::TRIAL_END_KEY])
                    : null,
                'ends_at' => null,
            ];

            // Salvar subscription
            $saved = $this->subscriptionRepository->saveSubscription($churchId, $subscriptionData);

            if (! $saved) {
                throw new GeneralExceptions(SubscriptionMessages::ERROR_SAVE_SUBSCRIPTION, 500);
            }

            // Atualizar dados de payment method na church (se fornecido)
            if ($paymentMethod && isset($paymentMethod[StripeRepository::BRAND_KEY]) && isset($paymentMethod[StripeRepository::LAST4_KEY])) {
                tenancy()->central(function () use ($churchId, $paymentMethod, $subscriptionData) {
                    $this->churchRepository->update(
                        [
                            'field' => 'id',
                            'operator' => '=',
                            'value' => $churchId,
                        ],
                        [
                            'pm_type' => $paymentMethod[StripeRepository::BRAND_KEY],
                            'pm_last_four' => $paymentMethod[StripeRepository::LAST4_KEY],
                            'trial_ends_at' => $subscriptionData['trial_ends_at'],
                        ]
                    );
                });
            }

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
