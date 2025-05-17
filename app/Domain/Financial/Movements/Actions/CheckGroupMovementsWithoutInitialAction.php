<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;

class CheckGroupMovementsWithoutInitialAction
{
    private MovementRepositoryInterface $movementRepository;

    /**
     * Constructor
     *
     * @param MovementRepositoryInterface $movementRepository
     */
    public function __construct(MovementRepositoryInterface $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    /**
     * Verifica se existem movimentações para o grupo mas não existe movimentação inicial
     *
     * @param int $groupId
     * @return bool
     */
    public function execute(int $groupId): bool
    {
        $movements = $this->movementRepository->getMovementsByGroup($groupId);
        $initialMovement = $this->movementRepository->getInitialMovementsByGroup($groupId);

        return !$movements->isEmpty() && is_null($initialMovement->id);
    }
}
