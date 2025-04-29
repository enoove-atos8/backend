<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Illuminate\Support\Collection;

class GetCurrentBalanceAction
{
    private MovementRepositoryInterface $movementRepository;

    public function __construct(MovementRepositoryInterface $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    /**
     * Get the current balance for a group
     *
     * @param int $groupId
     * @return float
     */
    public function execute(int $groupId): float
    {
        // Get all movements for the group
        $movements = $this->movementRepository->getMovementsByGroup($groupId);

        // If there are no movements, return 0
        if ($movements->isEmpty()) {
            return 0.0;
        }

        // Get the last movement which should have the current balance
        $lastMovement = $movements->sortByDesc('movementDate')->first();

        return $lastMovement->balance ?? 0.0;
    }
}
