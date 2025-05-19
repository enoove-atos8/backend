<?php

namespace Domain\Financial\Movements\Actions;

use Domain\Financial\Movements\Constants\ReturnMessages;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class ResetBalanceAction
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
     * @return mixed
     * @throws GeneralExceptions
     */
    public function execute(int $groupId): bool
    {
        $movementsReset = $this->movementRepository->resetBalance($groupId);

        if($movementsReset)
            return true;

        else
            throw new GeneralExceptions(ReturnMessages::MOVEMENTS_RESET_ERROR, 500);
    }
}
