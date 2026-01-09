<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;
use function Domain\Ecclesiastical\Groups\Actions\is_null;

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
    public function execute(int $id): Model | null
    {
        $group = $this->groupsRepository->getGroupsById($id);

        if(!is_null($group->id))
        {
            return $group;
        }
        else
        {
            return null;
        }
    }
}
