<?php

namespace Domain\Financial\Movements\Actions;

use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

class CreateMovementAction
{
    /**
     * @var MovementRepositoryInterface
     */
    private MovementRepositoryInterface $movementRepository;
    private UpdateMovementBalanceAction $updateMovementBalanceAction;
    private RecalculateBalanceAction $recalculateBalanceAction;

    /**
     * CreateMovementAction constructor.
     *
     * @param MovementRepositoryInterface $movementRepository
     * @param UpdateMovementBalanceAction $updateMovementBalanceAction
     * @param RecalculateBalanceAction $recalculateBalanceAction
     */
    public function __construct(
        MovementRepositoryInterface $movementRepository,
        UpdateMovementBalanceAction $updateMovementBalanceAction,
        RecalculateBalanceAction $recalculateBalanceAction,
    )
    {
        $this->movementRepository = $movementRepository;
        $this->updateMovementBalanceAction = $updateMovementBalanceAction;
        $this->recalculateBalanceAction = $recalculateBalanceAction;
    }

    /**
     * Execute the action.
     *
     * @param MovementsData $movementsData
     * @return void
     */
    public function execute(MovementsData $movementsData): void
    {
        $movementCreated = $this->movementRepository->createMovement($movementsData);

        if(!is_null($movementCreated->id))
            $this->recalculateBalanceAction->execute($movementsData->groupId);
    }
}
