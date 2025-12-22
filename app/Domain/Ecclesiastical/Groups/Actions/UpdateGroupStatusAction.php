<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;

class UpdateGroupStatusAction
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
    public function execute(int $groupId, bool $enabled): bool
    {
        $group = $this->groupsRepository->getGroupsById($groupId);

        if (! $group) {
            throw new GeneralExceptions(ReturnMessages::GROUP_NOT_FOUND, 404);
        }

        $updated = $this->groupsRepository->updateStatus($groupId, $enabled);

        if (! $updated) {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_GROUP_STATUS, 500);
        }

        return true;
    }
}
