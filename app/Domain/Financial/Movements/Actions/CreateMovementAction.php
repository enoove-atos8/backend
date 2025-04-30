<?php

namespace Domain\Financial\Movements\Actions;

use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;

class CreateMovementAction
{
    /**
     * @var MovementRepositoryInterface
     */
    private MovementRepositoryInterface $movementRepository;

    /**
     * CreateMovementAction constructor.
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
        $currentBalance = $movementsData->balance;
        $amount = $movementsData->amount;

        $newBalance = $movementsData->type === EntryRepository::ENTRY_TYPE
            ? $currentBalance + $amount
            : $currentBalance - $amount;

        $movementsData->balance = $newBalance;

        return $this->movementRepository->createMovement($movementsData);
    }
}
