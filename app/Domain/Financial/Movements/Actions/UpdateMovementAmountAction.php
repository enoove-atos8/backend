<?php

namespace App\Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;

class UpdateMovementAmountAction
{
    private MovementRepositoryInterface $movementsRepository;

    public function __construct(MovementRepositoryInterface $movementsRepository)
    {
        $this->movementsRepository = $movementsRepository;
    }

    /**
     *
     * @param int $movementId
     * @param float $newAmount
     * @return void
     */
    public function execute(int $movementId, float $newAmount): void
    {
        $this->movementsRepository->updateMovementAmount($movementId, $newAmount);
    }
}
