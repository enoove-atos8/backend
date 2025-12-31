<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class InstallmentsByPurchaseResourceCollection extends JsonResource
{
    /**
     * Disable wrapping
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $purchase = $this->resource['purchase'];
        $installments = [];

        foreach ($this->resource['installments'] ?? [] as $installment) {
            $installments[] = [
                'status' => $installment->status,
                'amount' => $installment->amount,
                'installment' => $installment->installment,
                'installmentAmount' => $installment->installmentAmount,
                'date' => $installment->date,
            ];
        }

        return [
            'purchase' => [
                'id' => $purchase?->id,
                'establishmentName' => $purchase?->establishmentName,
                'purchaseDescription' => $purchase?->purchaseDescription,
                'amount' => $purchase?->amount,
                'installments' => $purchase?->installments,
                'date' => $purchase?->date,
                'canPostpone' => $purchase?->canPostpone,
            ],
            'installments' => $installments,
        ];
    }
}
