<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class InstallmentsByPurchaseResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'installments';


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];
        $installments = $this->collection;

        foreach ($installments as $installment)
        {
            $result[] = [
               'status' => $installment->status,
               'amount' => $installment->amount,
               'installment' => $installment->installment,
               'installmentAmount'  => $installment->installmentAmount,
               'date'   =>  $installment->date,
            ];
        }

        return $result;
    }
}
