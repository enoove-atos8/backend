<?php

namespace Application\Api\v1\Ecclesiastical\Groups\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class GroupResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'groups';


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
                'id'            =>  $item->id,
                'name'          =>  $item->name,
                'enabled'       =>  $item->enabled,
                'leader'    =>  [
                    //'id'        =>  $item->member_id,
                    'fullName'  =>  $item->full_name,
                    'avatar'  =>  $item->avatar,
                    'contact' =>  $item->cell_phone,
                    'email'   =>  $item->email,
                ],
                'updatedAt'     =>  $item->updated_at
            ];
        }

        return $result;
    }
}
