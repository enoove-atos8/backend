<?php

namespace Domain\Financial\Movements\Actions;

use App\Domain\Financial\Movements\Actions\GetMovementByExitIdAction;
use Domain\Financial\Movements\Constants\ReturnMessages;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteMovementByExitId
{
    private MovementRepositoryInterface $movementRepository;

    private RecalculateBalanceAction $recalculateBalanceAction;

    private GetMovementByExitIdAction $getMovementByExitIdAction;

    /**
     * Constructor
     */
    public function __construct(
        MovementRepositoryInterface $movementRepository,
        RecalculateBalanceAction $recalculateBalanceAction,
        GetMovementByExitIdAction $getMovementByExitIdAction
    ) {
        $this->movementRepository = $movementRepository;
        $this->recalculateBalanceAction = $recalculateBalanceAction;
        $this->getMovementByExitIdAction = $getMovementByExitIdAction;
    }

    /**
     * Execute the action to delete all movements of a group
     *
     * @throws GeneralExceptions
     */
    public function execute(int $exitId): mixed
    {
        $result = $this->movementRepository->deleteMovementByEntryOrExitId(null, $exitId);

        if ($result) {
            $groupId = $this->getMovementByExitIdAction->execute($exitId)->groupId;
            $this->recalculateBalanceAction->execute($groupId);

            return $result;
        } else {
            throw new GeneralExceptions(ReturnMessages::MOVEMENTS_DELETE_ERROR, 500);
        }
    }
}
