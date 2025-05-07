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
    public function getDivisions(int $enabled): Collection;
    public function createDivision(DivisionData $divisionData): Division;
}
