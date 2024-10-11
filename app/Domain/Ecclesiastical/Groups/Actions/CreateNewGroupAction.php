<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Ecclesiastical\Groups\Models\Group;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class CreateNewGroupAction
{
    private GroupsRepository $groupsRepository;

    public function __construct(
        GroupRepositoryInterface $groupRepository,
    )
    {
        $this->groupsRepository = $groupRepository;
    }



    /**
     * @throws Throwable
     */
    public function __invoke(GroupData $groupData): Group
    {
        $group = $this->groupsRepository->newGroup($groupData);

        if(!is_null($group->id))
        {
            return $group;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_GROUP, 500);
        }
    }
}
