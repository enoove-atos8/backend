<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use App\Domain\Members\Actions\GetMemberLeaderAction;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionByNameAction;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Exception;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class GetGroupsByDivisionAction
{

    private GroupsRepository $groupsRepository;
    private GetDivisionByNameAction $getDivisionByNameAction;

    public function __construct(
        GroupRepositoryInterface  $groupsRepositoryInterface,
        GetDivisionByNameAction  $getDivisionByNameAction,
    )
    {
        $this->groupsRepository = $groupsRepositoryInterface;
        $this->getDivisionByNameAction = $getDivisionByNameAction;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $division): Collection | Paginator | array
    {
        $division = $this->getDivisionByNameAction->__invoke($division);
        $groups = $this->groupsRepository->getGroupsByDivision($division->id);

        if($groups->count() > 0)
        {
            return $groups;
        }
        else
        {
            return [];
        }
    }
}
