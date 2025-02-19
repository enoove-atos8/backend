<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use Domain\Ecclesiastical\Divisions\Actions\GetDivisionByNameAction;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class GetReturnReceivingGroupAction
{
    private GroupsRepository $groupsRepository;


    public function __construct(
        GroupRepositoryInterface  $groupsRepositoryInterface,
    )
    {
        $this->groupsRepository = $groupsRepositoryInterface;
    }


    /**
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function execute(): Model | null
    {
        $group = $this->groupsRepository->getReturnReceivingGroup();

        if(!is_null($group))
        {
            return $group;
        }
        else
        {
            return null;
        }
    }
}
