<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Throwable;

class GetDivisionByNameAction
{
    private DivisionRepository $divisionRepository;

    public function __construct(
        DivisionRepositoryInterface  $divisionRepositoryInterface,
    )
    {
        $this->divisionRepository = $divisionRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(string $division): Model | null
    {
        $division = $this->divisionRepository->getDivisionByName($division);

        return $division != null ? $division : null;
    }
}
