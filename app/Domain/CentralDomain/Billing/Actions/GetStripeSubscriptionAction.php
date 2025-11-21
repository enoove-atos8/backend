<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use App\Domain\CentralDomain\PaymentGateway\Actions\GetStripeSubscriptionDetailsAction;
use Domain\CentralDomain\Billing\DataTransferObjects\SubscriptionData;
use Domain\CentralDomain\Billing\Interfaces\SubscriptionRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PaymentGateway\StripeRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GetStripeSubscriptionAction
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private GetStripeSubscriptionDetailsAction $getStripeSubscriptionDetailsAction
    ) {
    }

    /**
     * @param int $churchId
     * @return SubscriptionData|null
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function execute(int $churchId): ?SubscriptionData
    {
        $subscription = $this->subscriptionRepository->getChurchSubscription($churchId);

        if (!$subscription || !$subscription->hasSubscription) {
            return $subscription;
        }

        // Buscar dados adicionais do Stripe via PaymentGateway
        try {
            $stripeData = $this->getStripeSubscriptionDetailsAction->execute($subscription->stripeSubscriptionId);

            if (!$stripeData) {
                return $subscription;
            }

            $stripeSubscription = $stripeData[StripeRepository::SUBSCRIPTION_KEY];
            $paymentMethod = $stripeData[StripeRepository::PAYMENT_METHOD_KEY];

            // Criar novo SubscriptionData com dados do Stripe
            return new SubscriptionData(
                stripeSubscriptionId: $subscription->stripeSubscriptionId,
                status: $stripeSubscription[StripeRepository::STATUS_KEY],
                nextBillingDate: isset($stripeSubscription[StripeRepository::CURRENT_PERIOD_END_KEY])
                    ? date('Y-m-d H:i:s', $stripeSubscription[StripeRepository::CURRENT_PERIOD_END_KEY])
                    : null,
                trialEndsAt: $subscription->trialEndsAt,
                onTrial: $subscription->onTrial,
                hasSubscription: true,
                paymentMethod: $paymentMethod,
            );
        } catch (\Exception $e) {
            // Se falhar ao buscar do Stripe, retorna os dados básicos do banco
            return $subscription;
        }
    }
}
