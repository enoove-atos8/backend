<?php

namespace Domain\Ecclesiastical\Groups\Interfaces;

use Domain\Ecclesiastical\Divisions\Models\Division;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Ecclesiastical\Groups\Models\Group;
use Illuminate\Support\Collection;

interface GroupRepositoryInterface
{
    public function getGroupsByDivision(Division $division): Collection;

    public function newGroup(GroupData $groupData): Group;
}
