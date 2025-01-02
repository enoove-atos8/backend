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
        $church = $this->resource['data']['church'];
        $user = $this->resource['data']['user'];
        $message = $this->resource['message'];

        return [
            'message'   =>  $message,
            'data'      =>  [
                'tenant_id'     =>  $church->tenant_id,
                'name'          =>  $church->name,
                'user'  =>  [
                    'email'    => $user->email,
                ]
            ]
        ];
    }
}
