<?php

namespace Infrastructure\Repositories\Ecclesiastical\Groups;

use Domain\Ecclesiastical\Divisions\Models\Division;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Ecclesiastical\Groups\Models\Group;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Infrastructure\Repositories\Member\MemberRepository;
use Illuminate\Database\Eloquent\Model;

class GroupsRepository extends BaseRepository implements GroupRepositoryInterface
{
    protected mixed $model = Group::class;
    const TABLE_NAME = 'ecclesiastical_divisions_groups';
    const MEMBER_TABLE_NAME = 'members';
    const ENABLED_TABLE_COLUMN = 'enabled';

    const ECCLESIASTICAL_DIVISION_ID_TABLE_COLUMN = 'ecclesiastical_divisions_groups.ecclesiastical_division_id';
    const ID_TABLE_COLUMN = 'ecclesiastical_divisions_groups.id';
    const LEADER_ID_COLUMN = 'ecclesiastical_divisions_groups.leader_id';
    const MEMBER_ECCLESIASTICAL_DIVISION_GROUPS_ID_COLUMN = 'members.ecclesiastical_divisions_group_id';
    const MEMBER_ID_COLUMN = 'members.id';
    const MEMBER_GROUP_LEADER_COLUMN = 'members.group_leader';
    const NAME_GROUP_COLUMN = 'name';

    const DISPLAY_SELECT_COLUMNS = [
        'ecclesiastical_divisions_groups.id as groups_id',
        'ecclesiastical_divisions_groups.ecclesiastical_division_id  as groups_division_id',
        'ecclesiastical_divisions_groups.parent_group_id as groups_parent_group_id',
        'ecclesiastical_divisions_groups.leader_id as groups_leader_id',
        'ecclesiastical_divisions_groups.name as groups_name',
        'ecclesiastical_divisions_groups.description as groups_description',
        'ecclesiastical_divisions_groups.financial_transactions_exists as groups_transactions_exists',
        'ecclesiastical_divisions_groups.enabled as groups_enabled',
        'ecclesiastical_divisions_groups.temporary_event as groups_temporary_event',
        'ecclesiastical_divisions_groups.return_values as groups_return_values',
        'ecclesiastical_divisions_groups.start_date as groups_start_date',
        'ecclesiastical_divisions_groups.end_date as groups_end_date',
        'ecclesiastical_divisions_groups.updated_at as groups_updated_at',
    ];

    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param Division $division
     * @return Collection
     */
    public function getGroupsByDivision(Model $division): Collection
    {
        return $this->getGroups($division);
    }


    /**
     * Get Groups and leaders members data
     */
    public function getGroups(Division $division = null): Collection
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            MemberRepository::DISPLAY_SELECT_COLUMNS
        );

        if($division != null)
        {
            if($division->require_leader == 1)
            {
                $q = DB::table(self::TABLE_NAME)
                    ->join(self::MEMBER_TABLE_NAME, self::LEADER_ID_COLUMN,
                        BaseRepository::OPERATORS['EQUALS'],
                        self::MEMBER_ID_COLUMN)
                    ->select($displayColumnsFromRelationship);
            }
        }
        else
        {
            $q = DB::table(self::TABLE_NAME)
                ->select(self::DISPLAY_SELECT_COLUMNS);
        }

        if($division != null)
            $q->where(self::ECCLESIASTICAL_DIVISION_ID_TABLE_COLUMN, $division->id);


        return $q->orderBy(self::NAME_GROUP_COLUMN, BaseRepository::ORDERS['ASC'])
            ->get();
    }



    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAllGroups(): Collection
    {
        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::ENABLED_TABLE_COLUMN, 1, 'and');

        return $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::ID_COLUMN,
            BaseRepository::ORDERS['ASC']
        );
    }




    /**
     * @param GroupData $groupData
     * @return Group
     */
    public function newGroup(GroupData $groupData): Group
    {
        return $this->create([
            'ecclesiastical_division_id'    =>   $groupData->divisionId,
            'parent_group_id'               =>   $groupData->parentGroupId,
            'leader_id'                     =>   $groupData->leaderId,
            'name'                          =>   $groupData->groupName,
            'description'                   =>   $groupData->description,
            'financial_transactions_exists' =>   $groupData->financialMovement,
            'enabled'                       =>   $groupData->enabled,
            'temporary_event'               =>   $groupData->temporaryEvent,
            'return_values'                 =>   $groupData->returnValues,
            'start_date'                    =>   $groupData->startDate,
            'end_date'                      =>   $groupData->endDate,
        ]);
    }
}
