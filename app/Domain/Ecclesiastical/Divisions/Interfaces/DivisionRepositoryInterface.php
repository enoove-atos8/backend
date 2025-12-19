<?php

namespace Domain\Ecclesiastical\Divisions\Interfaces;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface DivisionRepositoryInterface
{
    /**
     * @param string $division
     * @return DivisionData|null
     */
    public function getDivisionByName(string $division): ?DivisionData;
    public function getDivisionById(int $id): Model | null;
    public function getDivisions(?int $enabled = null): Collection;
    public function getDivisionsData(int $enabled = 1): Collection;
    public function createDivision(DivisionData $divisionData): Division;
    public function updateStatus(int $id, bool $enabled): bool;
    public function updateRequireLeader(int $id, bool $requireLeader): bool;
}
