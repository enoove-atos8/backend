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
            'billingInterval' => $this->billingInterval,
            'trialPeriodDays' => $this->trialPeriodDays,
            'features' => $this->features,
            'stripeProductId' => $this->stripeProductId,
            'stripePriceId' => $this->stripePriceId,
        ];
    }
}
