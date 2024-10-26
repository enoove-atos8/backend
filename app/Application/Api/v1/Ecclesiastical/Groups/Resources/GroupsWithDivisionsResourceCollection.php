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
                'routeResource'   =>  $item->route_resource,
                'name'             =>  $item->name,
                'description'      =>  $item->description,
                'enabled'          =>  $item->enabled,
                'groups'          =>   $this->getGroupsByDivision($this->collection['groups'], $item->id),
            ];
        }


        return $result;

    }



    /**
     *
     */
    public function getGroupsByDivision(Collection $groups, $divisionId): array
    {
        $result = [];

        foreach ($groups as $group)
        {
            if($group->ecclesiastical_division_id == $divisionId)
            {
                $result[] = [
                     'id'                            => $group->id,
                     'ecclesiasticalDivisionId'      => $group->ecclesiastical_division_id,
                     'parentGroupId'                 => $group->parent_group_id,
                     'leaderId'                     => $group->leader_id,
                     'name'                          => $group->name,
                     'description'                   => $group->description,
                     'financialTransactionsExists' => $group->transactions_exists,
                     'enabled'                       => $group->enabled,
                     'temporaryEvent'               => $group->temporary_event,
                     'returnValues'                 => $group->return_values,
                     'startDate'                    => $group->start_date,
                     'endDate'                      => $group->end_date,

                ];
            }
        }

        return $result;
    }
}


/**
 * [
 * 'id'               =>  $item->division_id,
 * 'route_resource'   =>  $item->division_route_resource,
 * 'name'             =>  $item->division_name,
 * 'description'      =>  $item->division_description,
 * 'enabled'          =>  $item->division_enabled,
 * 'groups' => [
 * 'id'                            => $item->groups_id,
 * 'ecclesiastical_division_id'    => $item->groups_division_id,
 * 'parent_group_id'               => $item->groups_parent_group_id,
 * 'leader_id'                     => $item->groups_leader_id,
 * 'name'                          => $item->groups_name,
 * 'description'                   => $item->groups_description,
 * 'financial_transactions_exists' => $item->groups_transactions_exists,
 * 'enabled'                       => $item->groups_enabled,
 * 'temporary_event'               => $item->groups_temporary_event,
 * 'return_values'                 => $item->groups_return_values,
 * 'start_date'                    => $item->groups_start_date,
 * 'end_date'                      => $item->groups_end_date,
 * ],
 * ];
 */
