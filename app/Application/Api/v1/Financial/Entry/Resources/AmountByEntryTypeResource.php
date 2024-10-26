<?php

namespace Application\Api\v1\Financial\Entry\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AmountByEntryTypeResource extends JsonResource
{
    public static $wrap = 'entriesIndicators';
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = $this->resource;

        return [
            'tithes'  => $result['tithes'],
            'offers'     => $result['offers'],
            'designated'    => $result['designated'],
            'devolution'    => $result['devolution'],
        ];
    }
}
