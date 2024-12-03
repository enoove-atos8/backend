<?php

namespace Application\Api\v1\Financial\Entries\Automation\Resources;

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
    private mixed $resultGroupReceived = null;
    private mixed $resultGroupReturned = null;


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        $result = [
            'notProcessed'     =>  [],
            'notMapped'        =>  [],
        ];

        foreach ($this->collection as $entry)
        {
            $this->getReceivedAndReturnedGroups($entry);

            if($entry->reading_error_receipt_reason == 'READING_ERROR')
                $result['notProcessed'][] = $this->jsonResponse($entry);

            if($entry->reading_error_receipt_reason == 'NOT_IMPLEMENTED')
                $result['notMapped'][] = $this->jsonResponse($entry);


            $this->resultGroupReceived = null;
            $this->resultGroupReturned = null;
        }

        return $result;
    }



    /**
     * @param $entry
     * @return array
     */
    public function jsonResponse($entry): array
    {
        return [
            'id'                 =>  $entry->reading_error_receipt_id,
            'groupReturned'      =>  $this->resultGroupReturned,
            'groupReceived'      =>  $this->resultGroupReceived,
            'entryType'          =>  $entry->reading_error_receipt_entry_type,
            'amount'             =>  $entry->reading_error_receipt_amount,
            'institution'        =>  $entry->reading_error_receipt_institution,
            'reason'             =>  $entry->reading_error_receipt_reason,
            'devolution'         =>  $entry->reading_error_receipt_devolution,
            'deleted'            =>  $entry->reading_error_receipt_deleted,
            'receiptLink'        =>  $entry->reading_error_receipt_receipt_link,
        ];
    }



    /**
     * @param $data
     * @return void
     */
    public function getReceivedAndReturnedGroups($data): void
    {
        if($data->g_received_id != null && is_null($this->resultGroupReceived))
        {
            $this->resultGroupReceived = [
                'id'      =>  $data->g_received_id,
                'name'    =>  $data->g_received_name,
            ];
        }

        if($data->reading_error_receipt_devolution == 1)
        {
            $this->resultGroupReturned = [
                'id'      =>  $data->g_returned_id,
                'name'    =>  $data->g_returned_name,
            ];
        }
    }


    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
