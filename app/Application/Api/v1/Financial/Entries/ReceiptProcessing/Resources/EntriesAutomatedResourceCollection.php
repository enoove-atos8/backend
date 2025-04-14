<?php

namespace Application\Api\v1\Financial\Entries\Automation\Resources;

use Domain\Members\Models\Member;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JsonSerializable;

class EntriesAutomatedResourceCollection extends ResourceCollection
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

            if($entry->entries_automation_reason == 'READING_ERROR')
                $result['notProcessed'][] = $this->jsonResponse($entry);

            if($entry->entries_automation_reason == 'NOT_IMPLEMENTED')
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
            'id'                 =>  $entry->entries_automation_id,
            'groupReturned'      =>  $this->resultGroupReturned,
            'groupReceived'      =>  $this->resultGroupReceived,
            'entryType'          =>  $entry->entries_automation_entry_type,
            'amount'             =>  $entry->entries_automation_amount,
            'institution'        =>  $entry->entries_automation_institution,
            'reason'             =>  $entry->entries_automation_reason,
            'devolution'         =>  $entry->entries_automation_devolution,
            'deleted'            =>  $entry->entries_automation_deleted,
            'receiptLink'        =>  $entry->entries_automation_receipt_link,
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

        if($data->entries_automation_devolution == 1)
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
