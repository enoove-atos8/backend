<?php

namespace Domain\Ecclesiastical\Divisions\Interfaces;

use Domain\Ecclesiastical\Divisions\Models\Division;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface DivisionRepositoryInterface
{
    public function getDivisionByName(string $division): Model;
    public function getDivisionIdByName(string $division): Model;
    public function getDivisions(int $enabled): Collection;
}
