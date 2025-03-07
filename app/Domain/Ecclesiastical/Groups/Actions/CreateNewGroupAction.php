<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use Domain\Ecclesiastical\Divisions\Actions\GetDivisionByIdAction;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionByNameAction;
use Domain\Ecclesiastical\Groups\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Ecclesiastical\Groups\Models\Group;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Util\Storage\S3\CreateDirectory;
use Throwable;

class CreateNewGroupAction
{
    private GroupsRepository $groupsRepository;
    private GetDivisionByIdAction $getDivisionByIdAction;

    private CreateDirectory $createDirectory;

    const BASE_PATH_DESIGNATED_ENTRIES_SHARED_RECEIPTS = 'sync_storage/financial/entries/shared_receipts/designated';

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        GetDivisionByIdAction   $getDivisionByIdAction,
        CreateDirectory $createDirectory,
    )
    {
        $this->groupsRepository = $groupRepository;
        $this->getDivisionByIdAction = $getDivisionByIdAction;
        $this->createDirectory = $createDirectory;
    }



    /**
     * @throws Throwable
     */
    public function execute(GroupData $groupData, string $tenant): Group
    {
        $existGroup = $this->groupsRepository->getGroupsByName($groupData->slug);

        if(is_null($existGroup))
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
        else
        {
            throw new GeneralExceptions(ReturnMessages::GROUP_ALREADY, 500);
        }

    }
}
