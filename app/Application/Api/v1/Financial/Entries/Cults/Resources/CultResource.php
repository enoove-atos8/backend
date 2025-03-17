<?php

namespace Application\Api\v1\Financial\Entries\Cults\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class CultResource extends JsonResource
{
    public static $wrap = 'cult';


    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id'                    =>  $this->resource->id,
            'cultDay'               =>  $this->resource->cult_day,
            'cultDate'              =>  $this->resource->cult_date,
            'depositCultDate'       =>  $this->resource->date_transaction_compensation,
            'amountOffer'           =>  $this->resource->amount_offer,
            'receipt'               =>  $this->resource->receipt,
            'entries'               =>  $this->resource->entries,
        ];
    }
}
