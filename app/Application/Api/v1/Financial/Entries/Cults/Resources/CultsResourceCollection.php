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
            $tithesAmount = $item->cults_tithes_amount;
            $designatedAmount = $item->cults_designated_amount;
            $offerAmount = $item->cults_offer_amount;

            $result[] = [
                'id'                    =>  $item->cults_id,
                'worshipWithoutEntries' =>  $item->cults_worship_without_entries,
                'cultDay'               =>  $item->cults_cult_day,
                'accountId'             =>  $item->cults_account_id,
                'cultDate'              =>  $item->cults_cult_date,
                'depositCultDate'       =>  $item->cults_date_transaction_compensation,
                'amountTithes'          =>  $tithesAmount,
                'amountDesignated'      =>  $designatedAmount,
                'amountOffer'           =>  $offerAmount,
                'totalAmount'           =>  $tithesAmount + $designatedAmount + $offerAmount,
                'receipt'               =>  $item->cults_receipt,
                'entries'               =>  $item->entries,
                'reviewer'              =>  [
                    'id'            =>  $item->financial_reviewers_id,
                    'fullName'      =>  $item->financial_reviewers_full_name,
                    'avatar'        =>  $item->financial_reviewers_avatar,
                ],
            ];
        }

        return $result;
    }
}
