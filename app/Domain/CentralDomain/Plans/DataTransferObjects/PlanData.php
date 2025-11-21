<?php

namespace Domain\CentralDomain\Plans\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PlanData extends DataTransferObject
{
    /** @var integer */
    public int $id = 0;

    /** @var string|null */
    public string|null $name;

    /** @var string|null */
    public string|null $description;

    /** @var float|null */
    public float|null $price;

    /** @var boolean|null */
    public bool|null $activated;

    /** @var string|null */
    public string|null $stripeProductId;

    /** @var string|null */
    public string|null $stripePriceId;

    /** @var string|null */
    public string|null $billingInterval;

    /** @var int|null */
    public int|null $trialPeriodDays;

    /** @var array|null */
    public array|null $features;

    /**
     * Create PlanData from database response
     *
     * @param array $data
     * @return self
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
            billingInterval: $data['billing_interval'] ?? null,
            trialPeriodDays: isset($data['trial_period_days']) ? (int) $data['trial_period_days'] : null,
            features: isset($data['features']) ? json_decode($data['features'], true) : null,
        );
    }
}
