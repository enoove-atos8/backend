<?php

namespace Application\Api\v1\Users\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class ErrorUserResource extends JsonResource
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
