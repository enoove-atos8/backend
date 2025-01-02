<?php

namespace Application\Api\v1\Financial\Entries\Reports\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ReportsRequestsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'reports';



    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];

        foreach ($this->collection as $item)
        {
            $result[] = [
                'id'                    =>  $item->id,
                'reportName'            =>  $item->report_name,
                'detailedReport'        =>  $item->detailed_report,
                'generationDate'        =>  $item->generation_date,
                'dates'                 =>  $item->dates,
                'status'                =>  $item->status,
                'startedBy'             =>  [
                    'id'    =>  $item->user->id,
                    'name'  =>  $item->user->detail->full_name,
                    'avatar'  =>  $item->user->detail->avatar,
                ],
                'entryTypes'            =>  $item->entry_types,
                'groupReceivedId'       =>  $item->group_received_id,
                'dateOrder'             =>  $item->date_order,
            ];
        }

        return $result;
    }
}
