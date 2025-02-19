<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Throwable;

class GetDivisionByIdAction
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
    public function execute(int $id): Model | null
    {
        $division = $this->divisionRepository->getDivisionById($id);

        return $division != null ? $division : null;
    }
}
