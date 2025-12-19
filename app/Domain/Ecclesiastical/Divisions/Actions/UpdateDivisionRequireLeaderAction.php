<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\Constants\ReturnMessages;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class UpdateDivisionRequireLeaderAction
{
    private DivisionRepositoryInterface $divisionRepository;

    public function __construct(
        DivisionRepositoryInterface $divisionRepository,
    ) {
        $this->divisionRepository = $divisionRepository;
    }

    /**
     * @throws Throwable
     */
    public function execute(int $id, bool $requireLeader): bool
    {
        $division = $this->divisionRepository->getDivisionById($id);

        if (is_null($division)) {
            throw new GeneralExceptions(ReturnMessages::DIVISION_NOT_FOUND, 404);
        }

        $updated = $this->divisionRepository->updateRequireLeader($id, $requireLeader);

        if (!$updated) {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_REQUIRE_LEADER, 500);
        }

        return true;
    }
}
