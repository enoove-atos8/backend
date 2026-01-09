<?php

namespace Application\Api\v1\Financial\ReceiptProcessing\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ReceiptsProcessingErrorResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'receipts';


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];
        $receipts = $this->collection;

        foreach ($receipts as $receipt)
        {
            $result [] = [
                'id'                            =>  $receipt->id,
                'docType'                       =>  $receipt->docType,
                'docSubType'                    =>  $receipt->docSubType,
                'reviewer'                      =>  [
                    'id'        =>  $receipt->reviewer->id,
                    'fullName'  =>  $receipt->reviewer->fullName,
                ],
                'payments'                      =>  [
                    'category'  =>  [
                        'id'                    =>  $receipt->paymentCategory->id,
                        'name'                  =>  $receipt->paymentCategory->name,
                    ],
                    'item'  =>  [
                        'id'                    =>  $receipt->paymentItem->id,
                        'name'                  =>  $receipt->paymentItem->name,
                    ],
                ],
                'groups'  =>  [
                    'received'  =>  [
                        'id'                        =>  $receipt->groupReceived->id,
                        'divisionId'                =>  $receipt->groupReceived->divisionId,
                        'name'                      =>  $receipt->groupReceived->name,
                    ],
                    'returned'  =>  [
                        'id'                        =>  $receipt->groupReturned->id,
                        'divisionId'                =>  $receipt->groupReturned->divisionId,
                        'name'                      =>  $receipt->groupReturned->name,
                    ],
                ],
                'amount'                        =>  $receipt->amount,
                'reason'                        =>  $receipt->reason,
                'status'                        =>  $receipt->status,
                'devolution'                    =>  $receipt->devolution,
                'transactionType'               =>  $receipt->transactionType,
                'transactionCompensation'       =>  $receipt->transactionCompensation,
                'dateTransactionCompensation'   =>  $receipt->dateTransactionCompensation,
                'receipt'                       =>  $receipt->receiptLink,
            ];
        }

        return $result;
    }
}
