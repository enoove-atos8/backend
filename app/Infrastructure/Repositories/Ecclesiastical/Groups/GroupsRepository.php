<?php

namespace Infrastructure\Repositories\Ecclesiastical\Groups;

use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Ecclesiastical\Groups\Models\Group;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use function Laravel\Prompts\table;

class GroupsRepository extends BaseRepository implements GroupRepositoryInterface
{
    protected mixed $model = Group::class;
    const TABLE_NAME = 'ecclesiastical_divisions_groups';
    const MEMBER_TABLE_NAME = 'members';

    const ECCLESIASTICAL_DIVISION_ID_TABLE_COLUMN = 'ecclesiastical_divisions_groups.ecclesiastical_division_id';
    const ID_TABLE_COLUMN = 'ecclesiastical_divisions_groups.id';
    const MEMBER_ECCLESIASTICAL_DIVISION_GROUPS_ID_COLUMN = 'members.ecclesiastical_divisions_group_id';
    const MEMBER_GROUP_LEADER_COLUMN = 'members.group_leader';

    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param int $divisionId
     * @return Collection
     */
    public function getGroupsByDivision(int $divisionId): Collection
    {
        return $this->getGroupsWithLeaderMember($divisionId);
    }



    /**
     * Get Groups and leaders members data
     */
    public function getGroupsWithLeaderMember(int $divisionId): Collection
    {
        return DB::table(self::TABLE_NAME)
            ->join(
                self::MEMBER_TABLE_NAME, self::ID_TABLE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                self::MEMBER_ECCLESIASTICAL_DIVISION_GROUPS_ID_COLUMN)
            ->where(self::ECCLESIASTICAL_DIVISION_ID_TABLE_COLUMN, $divisionId)
            ->where(self::MEMBER_GROUP_LEADER_COLUMN, true)
            ->get();
    }
}
