<?php

namespace Application\Api\v1\Financial\ReadingErrorReceipts\Resources;

use Domain\Members\Models\Member;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JsonSerializable;

class ReadingErrorReceiptsResourceCollection extends ResourceCollection
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
        return  $this->collection->map(function ($item){

            return [
                'id'                 =>  $item->id,
                'groupReturnedId'    =>  $item->group_returned_id,
                'groupReceivedId'    =>  $item->group_received_id,
                'entryType'          =>  $item->entry_type,
                'amount'             =>  $item->amount,
                'institution'        =>  $item->institution,
                'reason'             =>  $item->reason,
                'devolution'         =>  $item->devolution,
                'deleted'            =>  $item->deleted,
                'receiptLink'        =>  $item->receipt_link,
            ];
        });
    }


    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
