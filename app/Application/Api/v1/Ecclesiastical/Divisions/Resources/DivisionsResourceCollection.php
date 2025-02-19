<?php

namespace Application\Api\v1\Ecclesiastical\Divisions\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class DivisionsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'divisions';


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [];

        foreach ($this->collection as $item)
        {
            $result[] = [
                'id'                =>  $item->id,
                'slug'              =>  $item->slug,
                'name'              =>  $item->name,
                'desc'              =>  $item->description,
                'requireLeader'     =>  $item->require_leader,
                'createdAt'         =>  $item->created_at,
            ];
        }

        return $result;
    }
}
