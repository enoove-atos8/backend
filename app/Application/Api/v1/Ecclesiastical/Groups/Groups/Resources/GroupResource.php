<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources;

use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class GroupResource extends JsonResource
{
    public static $wrap = 'group';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = $this->resource;

        /** @var GroupData $this->resource */
        return [
            'id' => $result->id,
            'divisionId' => $result->divisionId,
            'parentGroupId' => $result->parentGroupId,
            'leaderId' => $result->leaderId,
            'name' => $result->name,
            'description' => $result->description,
            'slug' => $result->slug,
            'enabled' => $result->enabled,
        ];
    }
}
