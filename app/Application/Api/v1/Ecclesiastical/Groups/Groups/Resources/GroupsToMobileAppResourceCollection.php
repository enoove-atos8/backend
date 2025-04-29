<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class GroupsToMobileAppResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'groups';


    public function __construct($resource)
    {
        parent::__construct($resource);
    }


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        $result = [];

        foreach ($this->collection as $item)
        {
            $result[] = [
                'slugPage'       =>  $item->groups_slug,
                'titleCard'      =>  $item->groups_name,
                'descCard'       =>  $item->groups_description,
                'division'       =>  $item->groups_division_id,
                'divisionId'     =>  $item->groups_division_id,
                'groupId'        =>  $item->groups_id,
            ];
        }

        return $result;
    }
}
