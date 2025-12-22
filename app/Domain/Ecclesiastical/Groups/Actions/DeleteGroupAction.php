<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;

class DeleteGroupAction
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
    public function execute(int $groupId): bool
    {
        $group = $this->groupsRepository->getGroupsById($groupId);

        if (! $group) {
            throw new GeneralExceptions(ReturnMessages::GROUP_NOT_FOUND, 404);
        }

        $deleted = $this->groupsRepository->delete($groupId);

        if (! $deleted) {
            throw new GeneralExceptions(ReturnMessages::ERROR_DELETE_GROUP, 500);
        }

        return true;
    }
}
