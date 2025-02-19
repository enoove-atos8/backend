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
                'groups'           =>   $this->getGroupsByDivision($this->collection['groups'], $item->id),
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

                ];
            }
        }

        return $result;
    }
}
