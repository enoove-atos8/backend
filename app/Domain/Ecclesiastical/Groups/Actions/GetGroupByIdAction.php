<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class GetGroupByIdAction
{
    private GroupsRepository $groupsRepository;

    public function __construct(
        GroupRepositoryInterface $groupRepositoryInterface,
    )
    {
        $this->groupsRepository = $groupRepositoryInterface;
    }

    /**
     * @param int $id
     * @return GroupData|null
     * @throws UnknownProperties|GeneralExceptions
     */
    public function execute(int $id): ?GroupData
    {
        $group = $this->groupsRepository->getGroupById($id);

        if(!is_null($group))
        {
            return $group;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::GROUP_NOT_FOUND, 404);
        }

    }
}
