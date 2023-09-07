<?php

namespace Application\Api\v1\Entry\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class EntryResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'entries';


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($item){
            if(count($item->member()->get()) > 0)
                $member = $item->member()->first();
            else
                $member = null;

            $dataMember = [];

            if(!is_null($member)){
                $dataMember = ['id'=> $member->id, 'memberName'=> $member->full_name, 'memberAvatar'=> $member->avatar];
            }
            return [
                'id'                            =>  $item->id,
                'entryType'                     =>  $item->entry_type,
                'transactionType'               =>  $item->transaction_type,
                'transactionCompensation'       =>  $item->transaction_compensation,
                'dateTransactionCompensation'   =>  $item->date_transaction_compensation,
                'dateEntryRegister'             =>  $item->date_entry_register,
                'amount'                        =>  $item->amount,
                'recipient'                     =>  $item->recipient,
                'member'                        =>  $dataMember,
                'reviewer'                        =>  [
                    'reviewerId'      =>  1,
                    'reviewerName'    =>  'Jaime Junior da Silva Lopes',
                    'reviewerAvatar'  =>  'female-01.jpg',
                ],
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
