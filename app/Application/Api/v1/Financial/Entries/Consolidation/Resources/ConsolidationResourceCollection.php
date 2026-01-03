<?php

namespace Application\Api\v1\Financial\Entries\Consolidation\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ConsolidationResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'consolidations';



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
                'date'                  =>  $item->date,
                'startMonthDate'        =>  $item->created_at,
                'consolidated'          =>  $item->consolidated,
                'amountTithes'          =>  $item->tithe_amount,
                'amountDesignated'      =>  $item->designated_amount,
                'amountOffer'          =>  $item->offers_amount,
                'totalAmount'           =>  $item->total_amount,
            ];
        }

        return $result;
    }
}
