<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;

class CreateEntryMovementAction
{
    /**
     * @var MovementRepositoryInterface
     */
    private MovementRepositoryInterface $movementRepository;

    /**
     * CreateEntryMovementAction constructor.
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
     * @return mixed
     */
    public function execute(MovementsData $movementsData): mixed
    {
        // Calculate the new balance based on current balance and movement type
        $currentBalance = $movementsData->balance;
        $amount = $movementsData->amount;

        // For entry movements, add the amount to the balance
        // For exit movements, subtract the amount from the balance
        $newBalance = $movementsData->type === 'entry'
            ? $currentBalance + $amount
            : $currentBalance - $amount;

        // Update the balance in the DTO
        $movementsData->balance = $newBalance;

        return $this->movementRepository->createMovement($movementsData);
    }
}
