<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class PurchaseResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'purchases';


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {

        return $this->collection->map(function ($purchase) {
            return [
                'id' => $purchase->id,
                'cardId' => $purchase->cardId,
                'status' => $purchase->status,
                'amount' => $purchase->amount,
                'installments' => $purchase->installments,
                'installmentAmount' => $purchase->installmentAmount,
                'date' => $purchase->date,
                'receipt' => $purchase->receipt,
            ];
        });
    }
}
