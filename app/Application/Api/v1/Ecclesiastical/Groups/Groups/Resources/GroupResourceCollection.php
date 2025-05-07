<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
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

    private null | DivisionData $division;


    public function __construct($resource, $division = null)
    {
        parent::__construct($resource);
        $this->division = $division;
    }


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        if($this->division != null)
        {
            $result = [
                'divisionId'    =>  $this->division->id,
                'requireLeader' =>  $this->division->requireLeader,
                'data'          =>  []
            ];

            foreach ($this->collection as $item)
            {
                $result['data'][] = [
                    'id'            =>  $item->groups_id,
                    'divisionId'    =>  $item->groups_division_id,
                    'name'          =>  $item->groups_name,
                    'slug'          =>  $item->groups_slug,
                    'enabled'       =>  $item->groups_enabled,
                    'leader'        =>  $this->division->requireLeader == 1 ? [
                        'id'        =>  $item->members_id,
                        'fullName'  =>  $item->members_full_name,
                        'avatar'    =>  $item->members_avatar,
                        'cellPhone' =>  $item->members_cell_phone,
                        'email'     =>  $item->members_email,
                    ] : null,
                    'updatedAt'     =>  $item->groups_updated_at
                ];
            }
        }
        else
        {
            $result = [
                'data'          =>  []
            ];

            foreach ($this->collection as $item)
            {
                $result['data'][] = [
                    'id'            =>  $item->groups_id,
                    'divisionId'    =>  $item->groups_division_id,
                    'name'          =>  $item->groups_name,
                    'slug'          =>  $item->groups_slug,
                    'enabled'       =>  $item->groups_enabled,
                    'updatedAt'     =>  $item->groups_updated_at
                ];
            }
        }


        return $result;
    }
}
