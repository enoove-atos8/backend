<?php

namespace Application\Api\v1\Financial\Entries\Cults\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class CultsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'cults';



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
            $tithesAmount = $item->tithes_amount;
            $designatedAmount = $item->designated_amount;
            $offersAmount = $item->offers_amount;

            $reviewer = $item->reviewer()->get()[0];

            $result[] = [
                'id'                    =>  $item->id,
                'worshipWithoutEntries' =>  $item->worship_without_entries,
                'cultDay'               =>  $item->cult_day,
                'cultDate'              =>  $item->cult_date,
                'depositCultDate'       =>  $item->date_transaction_compensation,
                'amountTithes'          =>  $tithesAmount,
                'amountDesignated'      =>  $designatedAmount,
                'amountOffers'          =>  $offersAmount,
                'totalAmount'           =>  $tithesAmount + $designatedAmount + $offersAmount,
                'receipt'               =>  $item->receipt,
                'entries'               =>  $item->entries,
                'reviewer'              =>  [
                    'id'            =>  $reviewer->id,
                    'fullName'      =>  $reviewer->full_name,
                    'avatar'        =>  $reviewer->avatar,
                ],
            ];
        }

        return $result;
    }
}
