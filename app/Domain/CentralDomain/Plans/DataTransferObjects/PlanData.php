<?php

namespace Domain\CentralDomain\Plans\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PlanData extends DataTransferObject
{
    public int $id = 0;

    public ?string $name;

    public ?string $description;

    public ?float $price;

    public ?bool $activated;

    public ?string $stripeProductId;

    public ?string $stripePriceId;

    public ?bool $billingUnit;

    public ?string $billingInterval;

    public ?int $trialPeriodDays;

    public ?array $features;

    /**
     * Create PlanData from database response
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            activated: isset($data['activated']) ? (bool) $data['activated'] : null,
            stripeProductId: $data['stripe_product_id'] ?? null,
            stripePriceId: $data['stripe_price_id'] ?? null,
            billingUnit: isset($data['billing_unit']) ? (bool) $data['billing_unit'] : false,
            billingInterval: $data['billing_interval'] ?? null,
            trialPeriodDays: isset($data['trial_period_days']) ? (int) $data['trial_period_days'] : null,
            features: isset($data['features']) ? json_decode($data['features'], true) : null,
        );
    }
}
