<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Constants\ReturnMessages;
use App\Domain\Ecclesiastical\Groups\Groups\DataTransferObjects\GroupData;
use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GetGroupByIdAction
{
    private GroupsRepository $groupsRepository;

    public function __construct(
        GroupRepositoryInterface $groupRepositoryInterface,
    ) {
        $this->groupsRepository = $groupRepositoryInterface;
    }

    /**
     * @throws UnknownProperties|GeneralExceptions
     */
    public function execute(int $id): ?GroupData
    {
        $group = $this->groupsRepository->getGroupById($id);

        if (! is_null($group)) {
            return $group;
        } else {
            throw new GeneralExceptions(ReturnMessages::GROUP_NOT_FOUND, 404);
        }

    }
}
