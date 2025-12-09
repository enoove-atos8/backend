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
        // Busca o groupId ANTES de deletar o movimento
        $movement = $this->getMovementByExitIdAction->execute($exitId);
        $groupId = $movement?->groupId;

        $result = $this->movementRepository->deleteMovementByEntryOrExitId(null, $exitId);

        if ($result)
        {
            // Recalcula o saldo do grupo se o movimento tinha um grupo associado
            if ($groupId)
            {
                $this->recalculateBalanceAction->execute($groupId);
            }

            return $result;
        }

        // Se não conseguiu deletar, mas o movimento também não existia, considera sucesso
        if (!$movement)
        {
            return true;
        }

        throw new GeneralExceptions(ReturnMessages::MOVEMENTS_DELETE_ERROR, 500);
    }
}
