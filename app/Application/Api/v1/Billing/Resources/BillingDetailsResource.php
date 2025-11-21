<?php

namespace Application\Api\v1\Billing\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillingDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'plan' => $this->plan ? new PlanResource($this->plan) : null,
            'subscription' => $this->subscription ? new SubscriptionResource($this->subscription) : null,
        ];
    }
}
