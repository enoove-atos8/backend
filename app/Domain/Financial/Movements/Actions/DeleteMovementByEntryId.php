<?php

namespace Domain\Financial\Movements\Actions;

use App\Domain\Financial\Movements\Actions\GetMovementByEntryIdAction;
use Domain\Financial\Movements\Constants\ReturnMessages;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteMovementByEntryId
{
    private MovementRepositoryInterface $movementRepository;
    private RecalculateBalanceAction $recalculateBalanceAction;
    private GetMovementByEntryIdAction $getMovementByEntryIdAction;

    /**
     * Constructor
     *
     * @param MovementRepositoryInterface $movementRepository
     * @param RecalculateBalanceAction $recalculateBalanceAction
     * @param GetMovementByEntryIdAction $getMovementByEntryIdAction
     */
    public function __construct(
        MovementRepositoryInterface $movementRepository,
        RecalculateBalanceAction $recalculateBalanceAction,
        GetMovementByEntryIdAction $getMovementByEntryIdAction
    )
    {
        $this->movementRepository = $movementRepository;
        $this->recalculateBalanceAction = $recalculateBalanceAction;
        $this->getMovementByEntryIdAction = $getMovementByEntryIdAction;
    }

    /**
     * Execute the action to delete all movements of a group
     *
     * @param int $entryId
     * @return mixed
     * @throws GeneralExceptions
     */
    public function execute(int $entryId): mixed
    {
        $result = $this->movementRepository->deleteMovementByEntryOrExitId($entryId);

        if($result)
        {
            $groupId = $this->getMovementByEntryIdAction->execute($entryId)->groupId;
            $this->recalculateBalanceAction->execute($groupId);

            return $result;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::MOVEMENTS_DELETE_ERROR, 500);
        }
    }
}
