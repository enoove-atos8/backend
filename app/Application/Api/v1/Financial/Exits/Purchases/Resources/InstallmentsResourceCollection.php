<?php

namespace Application\Api\v1\Financial\Exits\Purchases\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class InstallmentsResourceCollection extends ResourceCollection
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
                'id' => $installment->id,
                'invoice'   =>  [
                    'id' => $installment->invoice->id,
                    'status' => $installment->invoice->status,
                    'amount' => $installment->invoice->amount
                ],
                'purchase'   => [
                    'id' => $installment->purchase->id,
                    'cardId' => $installment->purchase->cardId,
                    'status' => $installment->purchase->status,
                    'amount' => $installment->purchase->amount,
                    'installments' => $installment->purchase->installments,
                    'installmentsAmount' => $installment->purchase->installmentsAmount,
                    'date' => $installment->purchase->date,
                    'deleted' => $installment->purchase->deleted,
                    'receipt' => $installment->purchase->receipt,
                ],
                'status'    =>  $installment->status,
                'installment' => $installment->installment,
                'installmentAmount' => $installment->installmentAmount,
                'date'  =>  $installment->date,
                'deleted'  =>  $installment->deleted
            ];
        }

        return $result;
    }
}
