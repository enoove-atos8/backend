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
    public function __invoke($division): Model
    {
        $division = $this->divisionRepository->getDivisionByName($division);

        if($division->id != null)
        {
            return $division;
        }
        else
        {
            throw new GeneralExceptions('', 404);
        }
    }
}
