<?php

namespace Domain\Financial\Movements\Actions;

use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

class RecalculateBalanceAction
{
    private MovementRepositoryInterface $movementRepository;
    private UpdateMovementBalanceAction $updateMovementBalanceAction;

    public function __construct(
        MovementRepositoryInterface $movementRepository,
        UpdateMovementBalanceAction $updateMovementBalanceAction
    )
    {
        $this->movementRepository = $movementRepository;
        $this->updateMovementBalanceAction = $updateMovementBalanceAction;
    }

    public function execute(int $groupId): void
    {
        $currentBalance = 0.0;

        $initialBalance = $this->movementRepository->getInitialMovementsByGroup($groupId);

        if (!is_null($initialBalance->id) && !empty( $initialBalance->amount))
            $currentBalance = (float) $initialBalance->amount;

        $movements = $this->movementRepository->getMovementsByGroup($groupId, 'all', false);

        foreach ($movements as $movement)
        {
            $amount = (float) $movement->amount;

            if ($movement->type === EntryRepository::ENTRY_TYPE)
                $currentBalance += $amount;
            else if ($movement->type === ExitRepository::EXIT_TYPE)
                $currentBalance -= $amount;

            $currentBalance = max(0, $currentBalance);
            $this->updateMovementBalanceAction->execute($movement->id, $currentBalance);
        }
    }
}
