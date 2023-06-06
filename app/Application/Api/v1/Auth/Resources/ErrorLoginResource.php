<?php

namespace App\Application\Api\v1\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        if(count($this->resource) > 0 && $this->resource["status"] == 401)
            return ["Usuário não ativado!"];

        elseif(count($this->resource) > 0 && $this->resource["status"] == 404)
            return ["Usuário ou senha inválidos!"];
    }
}
