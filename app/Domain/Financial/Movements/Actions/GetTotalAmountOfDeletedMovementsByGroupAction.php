<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;

class GetTotalAmountOfDeletedMovementsByGroupAction
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
     * Execute the action to get the total amount of deleted movements for a group
     *
     * @param int $groupId
     * @return float
     */
    public function execute(int $groupId): float
    {
        return $this->movementRepository->getTotalAmountOfDeletedMovementsByGroup($groupId);
    }
}
