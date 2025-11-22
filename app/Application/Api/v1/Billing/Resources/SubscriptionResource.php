<?php

namespace Application\Api\v1\Billing\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'stripeSubscriptionId' => $this->stripeSubscriptionId,
            'status' => $this->status,
            'nextBillingDate' => $this->nextBillingDate,
            'trialEndsAt' => $this->trialEndsAt,
            'onTrial' => $this->onTrial,
            'hasSubscription' => $this->hasSubscription,
            'paymentMethod' => $this->paymentMethod,
        ];
    }
}
