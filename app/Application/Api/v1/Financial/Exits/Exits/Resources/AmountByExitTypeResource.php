<?php

namespace Application\Api\v1\Financial\Exits\Exits\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AmountByExitTypeResource extends JsonResource
{
    public static $wrap = 'indicators';



    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        $result = $this->resource;

        return [
            'payments'              => $result['payments'],
            'transfers'             => $result['transfers'],
            'ministerialTransfers'  => $result['ministerialTransfers'],
            'contributions'         => $result['contributions'],
        ];
    }
}
