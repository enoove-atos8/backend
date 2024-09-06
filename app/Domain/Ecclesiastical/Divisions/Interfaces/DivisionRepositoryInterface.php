<?php

namespace Domain\Ecclesiastical\Divisions\Interfaces;

use Domain\Ecclesiastical\Divisions\Models\Division;
use Illuminate\Database\Eloquent\Model;

interface DivisionRepositoryInterface
{
    public function getDivisionByName(string $division): Model;
}
