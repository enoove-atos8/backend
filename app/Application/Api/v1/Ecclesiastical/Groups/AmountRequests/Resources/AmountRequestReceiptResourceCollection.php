<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class AmountRequestReceiptResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     */
    public static $wrap = 'receipts';

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = ['data' => []];

        foreach ($this->collection as $item) {
            $result['data'][] = [
                'id' => $item->id ?? null,
                'amountRequestId' => $item->amountRequestId ?? null,
                'amount' => $item->amount ?? null,
                'description' => $item->description ?? null,
                'imageUrl' => $item->imageUrl ?? null,
                'receiptDate' => $item->receiptDate ?? null,
                'createdBy' => $item->createdBy ?? null,
                'createdAt' => $item->createdAt ?? null,
            ];
        }

        return $result;
    }
}
