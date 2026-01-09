<?php

namespace App\Domain\Ecclesiastical\Groups\Groups\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class GetFinancialGroupAction
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
     */
    public function execute(): Model | null
    {
        $group = $this->groupsRepository->getFinancialGroup();

        if(is_object($group))
        {
            return $group;
        }
        else
        {
            return null;
        }
    }
}
