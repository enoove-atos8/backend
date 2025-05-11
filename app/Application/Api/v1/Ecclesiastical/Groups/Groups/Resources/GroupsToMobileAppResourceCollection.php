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
                'slugPage'       =>  $item->slug,
                'titleCard'      =>  $item->name,
                'descCard'       =>  $item->description,
                'division'       =>  $item->divisionId,
                'divisionId'     =>  $item->divisionId,
                'groupId'        =>  $item->id,
            ];
        }

        return $result;
    }
}
