<?php

namespace Application\Api\v1\Church\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ChurchResource extends JsonResource
{
    public static $wrap = 'church';
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $church = $this->resource[0];
        $user = $this->resource[1];

        return [
            'tenant_id'     =>  $church->tenant_id,
            'name'          =>  $church->name,
            'user'  =>  [
                'email'    => $user->email,
            ]
        ];
    }
}
