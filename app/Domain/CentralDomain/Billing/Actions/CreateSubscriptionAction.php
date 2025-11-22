<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use App\Domain\CentralDomain\Billing\Constants\SubscriptionMessages;
use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Domain\CentralDomain\Plans\Interfaces\PlanRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PaymentGateway\StripeRepository;

class CreateSubscriptionAction
{
    public function __construct(
        private StripeRepositoryInterface $stripeRepository,
        private PlanRepositoryInterface $planRepository
    ) {}

    /**
     * Create a subscription for a church
     *
     * @param  string  $customerId  Stripe Customer ID
     * @param  string  $paymentMethodId  Stripe Payment Method ID
     * @param  int  $planId  Plan ID from database
     * @param  int|null  $memberCount  Number of members (for quantity-based plans)
     * @return array|null Subscription data
     *
     * @throws GeneralExceptions
     */
    public function execute(string $customerId, string $paymentMethodId, int $planId, ?int $memberCount = null): ?array
    {
        try {
            // 1. Buscar detalhes do plano
            $plan = $this->planRepository->getPlanById($planId);

            if (! $plan || ! $plan->stripePriceId) {
                throw new GeneralExceptions(SubscriptionMessages::ERROR_PLAN_NOT_FOUND, 404);
            }

            // 2. Anexar payment method ao customer
            $attached = $this->stripeRepository->attachPaymentMethod($paymentMethodId, $customerId);
            if (! $attached) {
                throw new GeneralExceptions(SubscriptionMessages::ERROR_ATTACH_PAYMENT_METHOD, 500);
            }

            // 3. Definir como payment method padrão
            $setDefault = $this->stripeRepository->setDefaultPaymentMethod($customerId, $paymentMethodId);
            if (! $setDefault) {
                throw new GeneralExceptions(SubscriptionMessages::ERROR_SET_DEFAULT_PAYMENT_METHOD, 500);
            }

            // 4. Criar subscription no Stripe
            $subscriptionOptions = [];

            // Adicionar trial period se configurado
            if ($plan->trialPeriodDays > 0) {
                $subscriptionOptions['trial_period_days'] = $plan->trialPeriodDays;
            }

            // Adicionar quantity se memberCount for fornecido
            \Log::info('CreateSubscriptionAction - memberCount recebido', ['memberCount' => $memberCount]);
            if ($memberCount !== null && $memberCount > 0) {
                $subscriptionOptions['quantity'] = $memberCount;
                \Log::info('CreateSubscriptionAction - quantity adicionada às options', ['quantity' => $memberCount]);
            }

            $subscription = $this->stripeRepository->createSubscription(
                $customerId,
                $plan->stripePriceId,
                $subscriptionOptions
            );

            if (! $subscription) {
                throw new GeneralExceptions(SubscriptionMessages::ERROR_CREATE_SUBSCRIPTION, 500);
            }

            // 5. Buscar detalhes do payment method para salvar no banco
            $paymentMethod = $this->stripeRepository->getPaymentMethod($paymentMethodId);

            return [
                StripeRepository::SUBSCRIPTION_KEY => $subscription,
                StripeRepository::PAYMENT_METHOD_KEY => $paymentMethod,
            ];

        } catch (GeneralExceptions $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new GeneralExceptions(SubscriptionMessages::ERROR_PROCESS_SUBSCRIPTION.': '.$e->getMessage(), 500);
        }
    }
}
