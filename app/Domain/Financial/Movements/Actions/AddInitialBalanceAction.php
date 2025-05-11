<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Actions\DeleteMovementsOfGroupAction;
use Domain\Financial\Movements\Constants\ReturnConstants;
use Domain\Financial\Movements\Constants\ReturnMessages;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Domain\Financial\Movements\Models\Movement;
use Illuminate\Support\Facades\DB;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class AddInitialBalanceAction
{
    /**
     * @var MovementRepositoryInterface
     */
    private MovementRepositoryInterface $movementRepository;

    /**
     * @var DeleteMovementsOfGroupAction
     */
    private DeleteMovementsOfGroupAction $deleteInitialBalanceMovementsAction;

    /**
     * @param MovementRepositoryInterface $movementRepository
     * @param DeleteMovementsOfGroupAction $deleteMovementsOfGroupAction
     */
    public function __construct(
        MovementRepositoryInterface         $movementRepository,
        DeleteMovementsOfGroupAction $deleteMovementsOfGroupAction
    )
    {
        $this->movementRepository = $movementRepository;
        $this->deleteInitialBalanceMovementsAction = $deleteMovementsOfGroupAction;
    }

    /**
     * Execute the action to add an initial balance
     *
     * @param MovementsData $movementsData
     * @return Movement
     * @throws GeneralExceptions
     */
    public function execute(MovementsData $movementsData): Movement
    {
        $existingInitialBalance = $this->movementRepository->getInitialMovementsByGroup($movementsData->groupId);

        if ($existingInitialBalance)
            throw new GeneralExceptions(ReturnConstants::GROUP_ALREADY_HAS_INITIAL_BALANCE, 422);

        $initialBalance = $this->movementRepository->addInitialBalance($movementsData);

        if(!is_null($initialBalance->id))
        {
            $deletedPreviousMovements = $this->deleteInitialBalanceMovementsAction->execute($movementsData->groupId);

            if($deletedPreviousMovements)
                return $initialBalance;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INITIAL_MOVEMENT_CREATE_ERROR, 500);
        }
    }
}
