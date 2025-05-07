<?php

namespace Domain\Ecclesiastical\Groups\Interfaces;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Ecclesiastical\Groups\Models\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface GroupRepositoryInterface
{
    public function getGroupsByDivision(DivisionData $division): Collection;
    public function getFinancialGroup(): Model | null;

    public function getGroups(DivisionData $divisionData = null): Collection;
    public function getGroupsById(int $id): Model | null;
    public function getGroupsByName(string $name): Model | null;
    public function getReturnReceivingGroup(): Model | null;

    public function getAllGroups(): Collection;

    public function save(GroupData $groupData): Group;
}
