<?php

namespace Application\Api\v1\Entry\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EntryResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $entry = $this->resource;

        if(count($entry->member()->get()) > 0) $member = $entry->member()->first(); else $member = null;

        $dataMember = null;

        if(!is_null($member)){
            $dataMember = ['id'=> $member->id, 'name'=> $member->full_name, 'avatar'=> $member->avatar];
        }
        return [
            'id'                            =>  $entry->id,
            'entryType'                     =>  $entry->entry_type,
            'transactionType'               =>  $entry->transaction_type,
            'transactionCompensation'       =>  $entry->transaction_compensation,
            'dateTransactionCompensation'   =>  $entry->date_transaction_compensation,
            'dateEntryRegister'             =>  $entry->date_entry_register,
            'amount'                        =>  $entry->amount,
            'devolution'                    =>  $entry->devolution,
            'recipient'                     =>  $entry->recipient,
            'deleted'                       =>  $entry->deleted,
            'member'                        =>  $dataMember,
            'reviewer'                        =>  [
                'reviewerId'      =>  1,
                'reviewerName'    =>  'Jaime Junior da Silva Lopes',
                'reviewerAvatar'  =>  'female-01.jpg',
            ],
        ];
    }
}
