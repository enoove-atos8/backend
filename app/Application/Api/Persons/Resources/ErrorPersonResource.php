<?php

namespace Application\Api\Persons\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorPersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        if(!$this->resource["data"] && $this->resource["status"] == 403)
            return ["Acesso negado"];
    }
}
