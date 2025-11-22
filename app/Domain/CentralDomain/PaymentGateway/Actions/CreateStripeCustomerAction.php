<?php

namespace Domain\CentralDomain\PaymentGateway\Actions;

use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
use Domain\CentralDomain\PaymentGateway\Interfaces\StripeRepositoryInterface;
use Infrastructure\Repositories\CentralDomain\PaymentGateway\StripeRepository;

class CreateStripeCustomerAction
{
    public function __construct(
        private StripeRepositoryInterface $stripeRepository
    ) {
    }

    /**
     * Create a Stripe Customer for the church
     *
     * @param ChurchData $churchData
     * @return string|null Stripe Customer ID
     */
    public function execute(ChurchData $churchData): ?string
    {
        try {
            $customerData = [
                StripeRepository::NAME_KEY => $churchData->tenantId,
                StripeRepository::EMAIL_KEY => $churchData->mail,
                StripeRepository::PHONE_KEY => $churchData->cellPhone,
                StripeRepository::METADATA_KEY => [
                    'tenant_id' => $churchData->tenantId,
                    'doc_type' => $churchData->docType,
                    'doc_number' => $churchData->docNumber,
                    'plan_id' => $churchData->planId,
                ],
            ];

            $customer = $this->stripeRepository->createCustomer($customerData);

            return $customer[StripeRepository::ID_KEY] ?? null;
        } catch (\Exception $e) {
            // Log error but don't fail church creation
            // The church can be created without Stripe customer
            return null;
        }
    }
}
