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
        $church = $this->church;
        $plan = $this->plan;
        $subscription = $this->subscription;

        // Construir plan com totalPrice
        $planData = null;
        if ($plan) {
            $planResource = new PlanResource($plan);
            $planArray = $planResource->toArray($request);

            // Calcular totalPrice considerando billing_unit
            if ($plan->billingUnit && $church && $church->memberCount) {
                // Se billing_unit = true, multiplica memberCount * price
                $planArray['totalPrice'] = $plan->price * $church->memberCount;
            } else {
                // Se billing_unit = false, retorna price normal
                $planArray['totalPrice'] = $plan->price;
            }

            $planData = $planArray;
        }

        return [
            'church' => $church ? [
                'memberCount' => $church->memberCount,
            ] : null,
            'plan' => $planData,
            'subscription' => $subscription ? new SubscriptionResource($subscription) : null,
        ];
    }
}
