<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Constants\ReturnMessages;
use App\Domain\Ecclesiastical\Groups\Groups\DataTransferObjects\GroupData;
use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;
use function Domain\Ecclesiastical\Groups\Actions\is_null;

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
