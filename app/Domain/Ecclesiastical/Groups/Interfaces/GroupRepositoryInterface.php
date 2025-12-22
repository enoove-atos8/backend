<?php

namespace Domain\Ecclesiastical\Groups\Interfaces;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Ecclesiastical\Groups\Models\Group;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface GroupRepositoryInterface
{
    public function getGroupsByDivision(DivisionData $division): Collection;

    public function getFinancialGroup(): ?Model;

    public function getGroups(?DivisionData $divisionData = null): Collection;

    public function getGroupsById(int $id): ?Model;

    public function getGroupsByName(string $name): ?Model;

    public function getReturnReceivingGroup(): ?Model;

    public function getAllGroups(): Collection;

    public function save(GroupData $groupData): Group;

    public function updateLeader(int $groupId, ?int $leaderId): bool;

    public function updateStatus(int $groupId, bool $enabled): bool;

    public function delete($id): bool;

    public function softDelete(int $groupId): bool;

    public function getGroupBalance(int $groupId): ?MovementsData;
}
