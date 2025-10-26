<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Throwable;

class GetDivisionsAction
{
    private DivisionRepositoryInterface $divisionRepository;

    public function __construct(
        DivisionRepositoryInterface  $divisionRepositoryInterface,
    )
    {
        $this->divisionRepository = $divisionRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function execute(int $enabled = 1): Collection
    {
        $divisions = $this->divisionRepository->getDivisions($enabled);

        if(count($divisions) > 0)
        {
            return $divisions;
        }
        else
        {
            throw new GeneralExceptions('', 404);
        }
    }
}
