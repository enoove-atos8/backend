<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Domain\Financial\Movements\Models\Movement;

class CreateExitMovementAction
{
    /**
     * @var MovementRepositoryInterface
     */
    private MovementRepositoryInterface $movementRepository;

    /**
     * CreateExitMovementAction constructor.
     *
     * @param MovementRepositoryInterface $movementRepository
     */
    public function __construct(MovementRepositoryInterface $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    /**
     * Execute the action.
     *
     * @param MovementsData $movementsData
     * @return Movement
     */
    public function execute(MovementsData $movementsData): Movement
    {
        return $this->movementRepository->createMovement($movementsData);
    }
}
