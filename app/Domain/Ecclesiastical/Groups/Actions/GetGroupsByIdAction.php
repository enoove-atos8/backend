<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class GetGroupsByIdAction
{
    private GroupsRepository $groupsRepository;

    public function __construct(
        GroupRepositoryInterface  $groupRepositoryInterface,
    )
    {
        $this->groupsRepository = $groupRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function __invoke(int $enabled = 1): Collection
    {
        $groups = $this->groupsRepository->getGroupsById();

        if(count($groups) > 0)
        {
            return $groups;
        }
        else
        {
            throw new GeneralExceptions('', 404);
        }
    }
}
