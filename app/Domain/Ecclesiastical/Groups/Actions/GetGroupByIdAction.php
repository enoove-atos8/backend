<?php

namespace Domain\Ecclesiastical\Groups\Actions;

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
     * @throws UnknownProperties
     */
    public function execute(int $id): ?GroupData
    {
        return $this->groupsRepository->getGroupById($id);

    }
}
