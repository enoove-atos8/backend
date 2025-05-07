<?php

namespace Domain\Ecclesiastical\Divisions\Actions;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
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
