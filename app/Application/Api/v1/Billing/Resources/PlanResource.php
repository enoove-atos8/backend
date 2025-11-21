<?php

namespace Application\Api\v1\Billing\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'billing_interval' => $this->billingInterval,
            'trial_period_days' => $this->trialPeriodDays,
            'features' => $this->features,
            'stripe_product_id' => $this->stripeProductId,
            'stripe_price_id' => $this->stripePriceId,
        ];
    }
}
