<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use App\Domain\Ecclesiastical\Groups\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class GetDivisionByGroupAction
{
    private DivisionRepositoryInterface $divisionRepository;
    private GroupRepositoryInterface $groupRepository;

    public function __construct(
        DivisionRepositoryInterface $divisionRepositoryInterface,
        GroupRepositoryInterface $groupRepositoryInterface
    )
    {
        $this->divisionRepository = $divisionRepositoryInterface;
        $this->groupRepository = $groupRepositoryInterface;
    }

    /**
     * Obtém a divisão associada a um grupo específico
     *
     * @param int $groupId ID do grupo
     * @return Model|null Retorna a divisão do grupo ou null se não encontrada
     * @throws Throwable
     */
    public function execute(int $groupId): Model|null
    {
        $group = $this->groupRepository->getGroupById($groupId);

        if (!$group)
            return null;

        return $this->divisionRepository->getDivisionById($group->divisionId);
    }
}
