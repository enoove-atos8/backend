<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateGroupLeaderAction
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository
    ) {}

    /**
     * @throws GeneralExceptions
     */
    public function execute(int $groupId, ?int $leaderId): bool
    {
        $updated = $this->groupRepository->updateLeader($groupId, $leaderId);

        if ($updated) {
            return true;
        }

        throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_GROUP_LEADER, 500);
    }
}
