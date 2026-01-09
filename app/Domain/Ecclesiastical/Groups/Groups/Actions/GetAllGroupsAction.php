<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;
use function Domain\Ecclesiastical\Groups\Actions\count;

class GetAllGroupsAction
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
    public function execute(int $enabled = 1): Collection
    {
        $groups = $this->groupsRepository->getGroups();

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
