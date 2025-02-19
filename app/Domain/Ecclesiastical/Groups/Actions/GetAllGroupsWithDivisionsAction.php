<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use Domain\Ecclesiastical\Divisions\Actions\GetDivisionsAction;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class GetAllGroupsWithDivisionsAction
{
    private GroupsRepository $groupsRepository;
    private GetDivisionsAction $getDivisionsAction;

    public function __construct(
        GroupRepositoryInterface  $groupRepositoryInterface,
        GetDivisionsAction  $getDivisionsAction,
    )
    {
        $this->groupsRepository = $groupRepositoryInterface;
        $this->getDivisionsAction = $getDivisionsAction;
    }



    /**
     * @throws Throwable
     */
    public function execute(int $enabled = 1): array
    {
        $groups = $this->groupsRepository->getAllGroups();
        $divisions = $this->getDivisionsAction->execute();

        if(count($groups) > 0)
        {
            return [
                'divisions' => $divisions,
                'groups'    =>  $groups,
            ];
        }
        else
        {
            throw new GeneralExceptions('', 404);
        }
    }
}
