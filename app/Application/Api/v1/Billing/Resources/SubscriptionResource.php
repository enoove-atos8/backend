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
            'stripe_subscription_id' => $this->stripeSubscriptionId,
            'status' => $this->status,
            'next_billing_date' => $this->nextBillingDate,
            'trial_ends_at' => $this->trialEndsAt,
            'on_trial' => $this->onTrial,
            'has_subscription' => $this->hasSubscription,
            'payment_method' => $this->paymentMethod,
        ];
    }
}
