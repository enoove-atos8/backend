<?php

namespace Domain\Ecclesiastical\Groups\Actions;

use App\Domain\Financial\Entries\Entries\Actions\GetHistoryTitheByMemberIdAction;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionsAction;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class GetAllGroupsByAllDivisionsAction
{
    private GroupsRepository $groupsRepository;

    private GetDivisionsAction $getDivisionsAction;

    private GetHistoryTitheByMemberIdAction $getHistoryTitheByMemberIdAction;

    public function __construct(
        GroupRepositoryInterface $groupsRepositoryInterface,
        GetDivisionsAction $getDivisionsAction,
        GetHistoryTitheByMemberIdAction $getHistoryTitheByMemberIdAction,
    ) {
        $this->groupsRepository = $groupsRepositoryInterface;
        $this->getDivisionsAction = $getDivisionsAction;
        $this->getHistoryTitheByMemberIdAction = $getHistoryTitheByMemberIdAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(): array
    {
        // Busca todas as divisões habilitadas
        $divisions = $this->getDivisionsAction->execute(1);

        $result = [];

        foreach ($divisions as $division) {
            $divisionData = new DivisionData(
                id: $division->id,
                slug: $division->route_resource,
                name: $division->name,
                description: $division->description,
                enabled: (bool) $division->enabled,
                requireLeader: (bool) $division->require_leader,
            );

            // Busca grupos da divisão
            $groups = $this->groupsRepository->getGroupsByDivision($divisionData);

            // Adiciona histórico de dízimos ao líder de cada grupo (quando existir)
            $groupsWithTitheHistory = $groups->map(function ($group) {
                if (isset($group->leader) && $group->leader->id) {
                    $group->leader->titheHistory = $this->getHistoryTitheByMemberIdAction->execute($group->leader->id);
                }

                return $group;
            });

            $result[] = [
                'division' => $divisionData,
                'groups' => $groupsWithTitheHistory,
            ];
        }

        return $result;
    }
}
