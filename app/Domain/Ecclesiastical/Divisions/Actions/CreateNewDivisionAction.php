<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Ecclesiastical\Groups\Models\Group;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
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
    public function __invoke(DivisionData $divisionData): Division
    {
        $division = $this->divisionRepository->createDivision($divisionData);

        if(!is_null($division->id))
        {
            return $division;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_GROUP, 500);
        }
    }
}
