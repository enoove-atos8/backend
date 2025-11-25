<?php

namespace Domain\CentralDomain\Billing\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class SubscriptionData extends DataTransferObject
{
    public ?string $stripeSubscriptionId;

    public ?string $status;

    public ?string $nextBillingDate;

    public ?string $trialEndsAt;

    public bool $onTrial;

    public bool $hasSubscription;

    public ?array $paymentMethod;

    public ?int $memberCount;

    /**
     * Create SubscriptionData from database response (subscriptions table)
     *
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
            memberCount: isset($data['quantity']) ? (int) $data['quantity'] : null,
        );
    }

    /**
     * Create new SubscriptionData from current instance with Stripe data
     *
     * @param  array  $stripeSubscription  Stripe subscription data
     * @param  array|null  $paymentMethod  Stripe payment method data
     *
     * @throws UnknownProperties
     */
    public static function fromSelf(self $current, array $stripeSubscription, ?array $paymentMethod = null): self
    {
        return new self(
            stripeSubscriptionId: $current->stripeSubscriptionId,
            status: $stripeSubscription['status'] ?? $current->status,
            nextBillingDate: isset($stripeSubscription['current_period_end'])
                ? date('Y-m-d H:i:s', $stripeSubscription['current_period_end'])
                : $current->nextBillingDate,
            trialEndsAt: $current->trialEndsAt,
            onTrial: $current->onTrial,
            hasSubscription: true,
            paymentMethod: $paymentMethod ?? $current->paymentMethod,
            memberCount: $stripeSubscription['items']['data'][0]['quantity'] ?? $current->memberCount,
        );
    }
}
