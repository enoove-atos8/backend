<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
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
     * @param string $division
     * @return DivisionData|null
     * @throws UnknownProperties
     */
    public function execute(string $division): ?DivisionData
    {
        return $this->divisionRepository->getDivisionByName($division);
    }
}
