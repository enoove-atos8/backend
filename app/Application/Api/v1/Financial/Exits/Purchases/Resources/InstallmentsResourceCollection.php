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
                    'id' => $installment->cardInvoiceData->id,
                    'status' => $installment->cardInvoiceData->status,
                    'amount' => $installment->cardInvoiceData->amount
                ],
                'purchase'   => [
                    'id' => $installment->cardPurchaseData->id,
                    'cardId' => $installment->cardPurchaseData->cardId,
                    'status' => $installment->cardPurchaseData->status,
                    'amount' => $installment->cardPurchaseData->amount,
                    'installments' => $installment->cardPurchaseData->installments,
                    'installmentAmount' => $installment->cardPurchaseData->installmentAmount,
                    'date' => $installment->cardPurchaseData->date,
                    'deleted' => $installment->cardPurchaseData->deleted,
                    'receipt' => $installment->cardPurchaseData->receipt,
                ],
                'installment' => [
                    'status'    =>  $installment->status,
                    'installment' => $installment->installment,
                    'installmentAmount' => $installment->installmentAmount,
                    'date'  =>  $installment->date,
                    'deleted'  =>  $installment->deleted
                ]
            ];
        }

        return $result;
    }
}
