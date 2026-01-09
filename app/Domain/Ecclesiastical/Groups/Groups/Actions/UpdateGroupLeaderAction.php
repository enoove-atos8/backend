<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Constants\ReturnMessages;
use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
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
