<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\Constants\ReturnMessages;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Throwable;

class GetDivisionsDataAction
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
        $divisions = $this->divisionRepository->getDivisionsData($enabled);

        if(count($divisions) > 0)
        {
            return $divisions;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::DIVISIONS_NOT_FOUND, 404);
        }
    }
}
