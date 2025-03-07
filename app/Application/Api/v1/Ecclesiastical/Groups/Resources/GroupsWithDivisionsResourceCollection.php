<?php

namespace Application\Api\v1\Ecclesiastical\Groups\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JsonSerializable;

class GroupsWithDivisionsResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = false;


    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = [
            'data'          =>  []
        ];

        foreach ($this->collection['divisions'] as $item)
        {
            $result['data'][] = [
                'id'               =>  $item->id,
                'slug'             =>  $item->slug,
                'name'             =>  $item->name,
                'description'      =>  $item->description,
                'enabled'          =>  $item->enabled,
                'groups'           =>   $this->getGroupsByDivision($this->collection['groups'], $item),
            ];
        }


        return $result;

    }



    /**
     *
     */
    public function getGroupsByDivision(Collection $groups, $division): array
    {
        $result = [];

        foreach ($groups as $group)
        {
            if($group->ecclesiastical_division_id == $division->id)
            {
                $result[] = [
                     'id'                           => $group->id,
                     'ecclesiasticalDivisionId'     => $group->ecclesiastical_division_id,
                     'parentGroupId'                => $group->parent_group_id,
                     'leaderId'                     => $group->leader_id,
                     'name'                         => $group->name,
                     'description'                  => $group->description,
                     'slug'                         => $group->slug,
                     'financialTransactionsExists'  => $group->transactions_exists,
                     'enabled'                      => $group->enabled,
                     'temporaryEvent'               => $group->temporary_event,
                     'returnValues'                 => $group->return_values,
                     'returnReceivingGroup'         => $group->return_receiving,
                     'startDate'                    => $group->start_date,
                     'endDate'                      => $group->end_date,
                     'division'                     => $this->getDivisionById($this->collection['divisions'], $group->ecclesiastical_division_id),

                ];
            }
        }

        return $result;
    }



    public function getDivisionById(Collection $divisions, $divisionId): array
    {
        $result = [];

        foreach ($divisions as $division)
        {
            if($division->id == $divisionId)
            {
                $result[] = [
                    'id'       => $division->id,
                    'name'     => $division->name,
                    'slug'     => $division->route_resource,

                ];
            }
        }

        return $result;
    }
}
