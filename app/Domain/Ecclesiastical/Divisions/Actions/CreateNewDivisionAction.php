<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Domain\Ecclesiastical\Divisions\Constants\ReturnMessages;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Throwable;

class CreateNewDivisionAction
{
    private DivisionRepository $divisionRepository;


    public function __construct(
        DivisionRepositoryInterface $divisionRepository,
    )
    {
        $this->divisionRepository = $divisionRepository;
    }



    /**
     * @throws Throwable
     */
    public function execute(DivisionData $divisionData, $tenant): Division
    {
        $existDivision = $this->divisionRepository->getDivisionByName($divisionData->slug);

        if(is_null($existDivision))
        {
            $division = $this->divisionRepository->createDivision($divisionData);

            if(!is_null($division->id))
            {
                return $division;
            }
            else
            {
                throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_DIVISION, 500);
            }
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_ALREADY_DIVISION, 500);
        }
    }
}
