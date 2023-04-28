<?php

namespace Infrastructure\Repositories\Person;

use App\Domain\Persons\Models\Person;
use Domain\Persons\DataTransferObjects\PersonData;
use Domain\Persons\Interfaces\PersonRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class PersonRepository extends BaseRepository implements PersonRepositoryInterface
{
    /**
     * PersonRepository constructor.
     *
     * @param Person $model
     */
    public function __construct(Person $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function createPerson(PersonData $personData): Person
    {
        // TODO: Implement createPerson() method.
    }
}
