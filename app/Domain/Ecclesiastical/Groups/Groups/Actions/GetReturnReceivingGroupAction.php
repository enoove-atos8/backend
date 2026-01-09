<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class GetReturnReceivingGroupAction
{
    private GroupsRepository $groupsRepository;

    public function __construct(
        GroupRepositoryInterface $groupsRepositoryInterface,
    ) {
        $this->groupsRepository = $groupsRepositoryInterface;
    }

    /**
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function execute(): ?Model
    {
        $group = $this->groupsRepository->getReturnReceivingGroup();

        if (! is_null($group)) {
            return $group;
        } else {
            return null;
        }
    }
}
