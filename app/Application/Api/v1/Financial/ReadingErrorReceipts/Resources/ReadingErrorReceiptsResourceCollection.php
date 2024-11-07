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
        $result = [
            'notProcessed'     =>  [],
            'notMapped'        =>  [],
        ];

        foreach ($this->collection as $entry)
        {
            if($entry->reason == 'READING_ERROR')
                $result['notProcessed'][] = $this->mount($entry);

            if($entry->reason == 'NOT_IMPLEMENTED')
                $result['notMapped'][] = $this->mount($entry);
        }

        return $result;
    }



    /**
     * @param $data
     * @return array
     */
    public function mount($data): array
    {
        return [
            'id'                 =>  $data->id,
            'groupReturnedId'    =>  $data->group_returned_id,
            'groupReceivedId'    =>  $data->group_received_id,
            'entryType'          =>  $data->entry_type,
            'amount'             =>  $data->amount,
            'institution'        =>  $data->institution,
            'reason'             =>  $data->reason,
            'devolution'         =>  $data->devolution,
            'deleted'            =>  $data->deleted,
            'receiptLink'        =>  $data->receipt_link,
        ];
    }


    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
