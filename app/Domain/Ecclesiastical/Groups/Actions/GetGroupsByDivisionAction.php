<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use App\Domain\Financial\Entries\Entries\Actions\GetHistoryTitheByMemberIdAction;
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

    private GetHistoryTitheByMemberIdAction $getHistoryTitheByMemberIdAction;

    public function __construct(
        GroupRepositoryInterface $groupsRepositoryInterface,
        GetDivisionByNameAction $getDivisionByNameAction,
        GetHistoryTitheByMemberIdAction $getHistoryTitheByMemberIdAction,
    ) {
        $this->groupsRepository = $groupsRepositoryInterface;
        $this->getDivisionByNameAction = $getDivisionByNameAction;
        $this->getHistoryTitheByMemberIdAction = $getHistoryTitheByMemberIdAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(string $division, ?bool $active = null): Collection|Paginator|array
    {
        $division = $this->getDivisionByNameAction->execute($division);

        if (is_null($division)) {
            return [];
        }

        $groups = $this->groupsRepository->getGroupsByDivision($division, $active);

        if ($groups->count() === 0) {
            return [];
        }

        // Adiciona histórico de dízimos ao líder de cada grupo (quando existir)
        return $groups->map(function ($group) {
            if (isset($group->leader) && $group->leader->id) {
                $group->leader->titheHistory = $this->getHistoryTitheByMemberIdAction->execute($group->leader->id);
            }

            return $group;
        });
    }
}
