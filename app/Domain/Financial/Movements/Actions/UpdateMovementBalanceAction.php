<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;

class UpdateMovementBalanceAction
{
    private MovementRepositoryInterface $movementsRepository;

    public function __construct(MovementRepositoryInterface $movementsRepository)
    {
        $this->movementsRepository = $movementsRepository;
    }

    /**
     * Executa a atualização do saldo de um movimento.
     *
     * @param int $movementId
     * @param float $newBalance
     * @return void
     */
    public function execute(int $movementId, float $newBalance): void
    {
        $this->movementsRepository->updateMovementBalance($movementId, $newBalance);
    }
}
