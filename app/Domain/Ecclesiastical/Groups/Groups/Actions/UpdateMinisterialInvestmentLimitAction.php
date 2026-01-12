<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Constants\ReturnMessages;
use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;

class UpdateMinisterialInvestmentLimitAction
{
    private GroupsRepository $groupsRepository;

    public function __construct(
        GroupRepositoryInterface $groupsRepositoryInterface,
    ) {
        $this->groupsRepository = $groupsRepositoryInterface;
    }

    /**
     * @throws GeneralExceptions
     */
    public function execute(int $groupId, ?float $limit): bool
    {
        $group = $this->groupsRepository->getGroupsById($groupId);

        if (! $group) {
            throw new GeneralExceptions(ReturnMessages::GROUP_NOT_FOUND, 404);
        }

        $updated = $this->groupsRepository->updateMinisterialInvestmentLimit($groupId, $limit);

        if (! $updated) {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_MINISTERIAL_LIMIT, 500);
        }

        return true;
    }
}
