<?php

namespace Domain\Persons\Interfaces;

use App\Domain\Persons\Models\Person;
use Domain\Persons\DataTransferObjects\PersonData;
use Illuminate\Support\Collection;

interface PersonRepositoryInterface
{
    public function all(): Collection;

    public function createPerson(PersonData $personData): Person;
}
