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
        return [
            'id'        =>  $entry->id,
            'message'   =>  'Entrada cadastrada com sucesso!!!',
        ];
    }
}
