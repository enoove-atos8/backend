<?php

namespace Infrastructure\Repositories\Ecclesiastical\Groups;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
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
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GroupsRepository extends BaseRepository implements GroupRepositoryInterface
{
    protected mixed $model = Group::class;
    const TABLE_NAME = 'ecclesiastical_divisions_groups';
    const GROUP_RECEIVED_TABLE_NAME = 'ecclesiastical_divisions_groups as group_received';
    const GROUP_RETURNED_TABLE_NAME = 'ecclesiastical_divisions_groups as group_returned';
    const MEMBER_TABLE_NAME = 'members';
    const ENABLED_TABLE_COLUMN = 'enabled';
    const FINANCIAL_GROUP_COLUMN = 'financial_group';
    const RETURN_RECEIVING_TABLE_COLUMN = 'return_receiving';

    const ECCLESIASTICAL_DIVISION_ID_TABLE_COLUMN = 'ecclesiastical_divisions_groups.ecclesiastical_division_id';
    const ID_TABLE_COLUMN = 'ecclesiastical_divisions_groups.id';
    const ID_COLUMN_JOINED = 'ecclesiastical_divisions_groups.id';
    const LEADER_ID_COLUMN = 'ecclesiastical_divisions_groups.leader_id';
    const MEMBER_ECCLESIASTICAL_DIVISION_GROUPS_ID_COLUMN = 'members.ecclesiastical_divisions_group_id';
    const MEMBER_ID_COLUMN = 'members.id';
    const MEMBER_GROUP_LEADER_COLUMN = 'members.group_leader';
    const NAME_GROUP_COLUMN = 'name';
    const SLUG_GROUP_COLUMN = 'slug';

    const DISPLAY_SELECT_COLUMNS = [
        'ecclesiastical_divisions_groups.id as groups_id',
        'ecclesiastical_divisions_groups.ecclesiastical_division_id  as groups_division_id',
        'ecclesiastical_divisions_groups.parent_group_id as groups_parent_group_id',
        'ecclesiastical_divisions_groups.leader_id as groups_leader_id',
        'ecclesiastical_divisions_groups.name as groups_name',
        'ecclesiastical_divisions_groups.description as groups_description',
        'ecclesiastical_divisions_groups.slug as groups_slug',
        'ecclesiastical_divisions_groups.financial_transactions_exists as groups_financial_transactions_exists',
        'ecclesiastical_divisions_groups.enabled as groups_enabled',
        'ecclesiastical_divisions_groups.temporary_event as groups_temporary_event',
        'ecclesiastical_divisions_groups.return_values as groups_return_values',
        'ecclesiastical_divisions_groups.financial_group as groups_financial_group',
        'ecclesiastical_divisions_groups.start_date as groups_start_date',
        'ecclesiastical_divisions_groups.end_date as groups_end_date',
        'ecclesiastical_divisions_groups.updated_at as groups_updated_at',
    ];

    const DISPLAY_SELECT_GROUP_WITH_RECEIVED_ALIAS = [
        'g_received.id AS g_received_id',
        'g_received.ecclesiastical_division_id AS g_received_division_id',
        'g_received.parent_group_id AS g_received_parent_group_id',
        'g_received.leader_id AS g_received_leader_id',
        'g_received.name AS g_received_name',
        'g_received.description AS g_received_description',
        'g_received.slug AS g_received_slug',
        'g_received.financial_transactions_exists AS g_received_transactions_exists',
        'g_received.enabled AS g_received_enabled',
        'g_received.temporary_event AS g_received_temporary_event',
        'g_received.return_values AS g_received_return_values',
        'g_received.financial_group AS g_received_financial_group',
        'g_received.start_date AS g_received_start_date',
        'g_received.end_date AS g_received_end_date',
        'g_received.updated_at AS g_received_updated_at',
    ];

    const DISPLAY_SELECT_GROUP_WITH_RETURNED_ALIAS = [
        'g_returned.id AS g_returned_id',
        'g_returned.ecclesiastical_division_id AS g_returned_division_id',
        'g_returned.parent_group_id AS g_returned_parent_group_id',
        'g_returned.leader_id AS g_returned_leader_id',
        'g_returned.name AS g_returned_name',
        'g_returned.description AS g_returned_description',
        'g_returned.slug AS g_returned_slug',
        'g_returned.financial_transactions_exists AS g_returned_transactions_exists',
        'g_returned.enabled AS g_returned_enabled',
        'g_returned.temporary_event AS g_returned_temporary_event',
        'g_returned.return_values AS g_returned_return_values',
        'g_returned.financial_group AS g_returned_financial_group',
        'g_returned.start_date AS g_returned_start_date',
        'g_returned.end_date AS g_returned_end_date',
        'g_returned.updated_at AS g_returned_updated_at',
    ];

    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param DivisionData $division
     * @return Collection
     */
    public function getGroupsByDivision(DivisionData $division): Collection
    {
        $groups = $this->getGroups($division);

        return $groups->map(fn($item) => GroupData::fromResponse((array) $item));
    }


    /**
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getFinancialGroup(): Model | null
    {
        return $this->getItemByColumn(
            self::FINANCIAL_GROUP_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            true
        );
    }


    /**
     * Get Groups and leaders members data
     */
    public function getGroups(DivisionData $divisionData = null): Collection
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            MemberRepository::DISPLAY_SELECT_COLUMNS
        );

        if($divisionData != null)
        {
            if($divisionData->requireLeader == 1)
            {
                $q = DB::table(self::TABLE_NAME)
                    ->join(self::MEMBER_TABLE_NAME, self::LEADER_ID_COLUMN,
                        BaseRepository::OPERATORS['EQUALS'],
                        self::MEMBER_ID_COLUMN)
                    ->select($displayColumnsFromRelationship);
            }
            else
            {
                $q = DB::table(self::TABLE_NAME)
                    ->select(self::DISPLAY_SELECT_COLUMNS);
            }
        }
        else
        {
            $q = DB::table(self::TABLE_NAME)
                ->select(self::DISPLAY_SELECT_COLUMNS);
        }

        if($divisionData != null)
            $q->where(self::ECCLESIASTICAL_DIVISION_ID_TABLE_COLUMN, $divisionData->id);

        $q->where(self::ENABLED_TABLE_COLUMN, 1);


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
     * @param int $id
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getGroupsById(int $id): Model | null
    {
        return $this->getItemByColumn(
            self::ID_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            $id
        );
    }

    /**
     * Recupera um grupo por ID como um objeto GroupData
     * @param int $id
     * @return GroupData|null
     * @throws UnknownProperties
     */
    public function getGroupById(int $id): ?GroupData
    {
        $displayColumnsFromRelationship = array_merge(
            self::DISPLAY_SELECT_COLUMNS,
            MemberRepository::DISPLAY_SELECT_COLUMNS
        );

        $group = $this->model
            ->select($displayColumnsFromRelationship)
            ->leftJoin(
                self::MEMBER_TABLE_NAME,
                self::LEADER_ID_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                MemberRepository::ID_COLUMN_JOINED
            )
            ->where(self::ID_COLUMN_JOINED, $id)
            ->where(self::TABLE_NAME . '.' . self::ENABLED_TABLE_COLUMN, 1)
            ->first();

        if (!$group) {
            return null;
        }

        $attributes = $group->getAttributes();
        return GroupData::fromResponse($attributes);
    }


    /**
     * @param string $name
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getGroupsByName(string $name): Model | null
    {
        return $this->getItemByColumn(
            self::SLUG_GROUP_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            $name
        );
    }




    /**
     * @param GroupData $groupData
     * @return Group
     */
    public function save(GroupData $groupData): Group
    {
        return $this->create([
            'ecclesiastical_division_id'    =>   $groupData->divisionId,
            'parent_group_id'               =>   $groupData->parentGroupId,
            'leader_id'                     =>   $groupData->leaderId,
            'name'                          =>   $groupData->name,
            'description'                   =>   $groupData->description,
            'slug'                          =>   $groupData->slug,
            'financial_transactions_exists' =>   $groupData->financialMovement,
            'enabled'                       =>   $groupData->enabled,
            'temporary_event'               =>   $groupData->temporaryEvent,
            'return_values'                 =>   $groupData->returnValues,
            'financial_group'               =>   $groupData->financialGroup,
            'start_date'                    =>   $groupData->startDate,
            'end_date'                      =>   $groupData->endDate,
        ]);
    }


    /**
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getReturnReceivingGroup(): Model | null
    {
        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::RETURN_RECEIVING_TABLE_COLUMN, 1, 'and');

        return $this->getItemWithRelationshipsAndWheres(
            $this->queryConditions,
            self::NAME_GROUP_COLUMN,
            ['*'],
            BaseRepository::ORDERS['ASC']
        );
    }
}
