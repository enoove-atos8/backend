<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use App\Domain\CentralDomain\Billing\Constants\SubscriptionMessages;
use App\Domain\CentralDomain\Churches\Church\Actions\UpdateChurchAction;
use Domain\CentralDomain\Billing\Interfaces\SubscriptionRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PaymentGateway\StripeRepository;

class SaveSubscriptionAction
{
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

            // Salvar subscription (repository mapeia os dados do Stripe)
            $subscriptionData = $this->subscriptionRepository->saveSubscription($churchId, $subscription);

            // Preparar dados para atualizar church
            $churchUpdateData = [
                'trial_ends_at' => $subscriptionData['trial_ends_at'],
                'member_count' => $subscriptionData['quantity'],
            ];

            // Adicionar payment method se fornecido
            if ($paymentMethod && isset($paymentMethod[StripeRepository::BRAND_KEY]) && isset($paymentMethod[StripeRepository::LAST4_KEY])) {
                $churchUpdateData['pm_type'] = $paymentMethod[StripeRepository::BRAND_KEY];
                $churchUpdateData['pm_last_four'] = $paymentMethod[StripeRepository::LAST4_KEY];
            }

            // Atualizar dados da church
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
