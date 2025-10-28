<?php

namespace App\Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;

class GetMovementByExitIdAction
{
    private MovementRepositoryInterface $movementRepository;

    /**
     * GetMovementsByGroupAction constructor.
     */
    public function __construct(MovementRepositoryInterface $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    /**
     * Execute the action to get movements by group
     */
    public function execute(int $exitId): ?MovementsData
    {
        return $this->movementRepository->getMovementsByExitIdAction($exitId);
    }
}
