<?php

namespace Domain\CentralDomain\Billing\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class SubscriptionData extends DataTransferObject
{
    /** @var string|null */
    public string|null $stripeSubscriptionId;

    /** @var string|null */
    public string|null $status;

    /** @var string|null */
    public string|null $nextBillingDate;

    /** @var string|null */
    public string|null $trialEndsAt;

    /** @var bool */
    public bool $onTrial;

    /** @var bool */
    public bool $hasSubscription;

    /** @var array|null */
    public array|null $paymentMethod;

    /**
     * Create SubscriptionData from database response (subscriptions table)
     *
     * @param array $data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            stripeSubscriptionId: $data['stripe_id'] ?? null,
            status: $data['stripe_status'] ?? null,
            nextBillingDate: null, // Será preenchido com dados do Stripe
            trialEndsAt: $data['trial_ends_at'] ?? null,
            onTrial: isset($data['trial_ends_at']) && strtotime($data['trial_ends_at']) > time(),
            hasSubscription: isset($data['stripe_id']),
            paymentMethod: null, // Será preenchido com dados do Stripe
        );
    }
}
