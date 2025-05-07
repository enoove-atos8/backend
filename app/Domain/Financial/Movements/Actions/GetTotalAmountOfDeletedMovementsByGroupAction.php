<?php

namespace Domain\Financial\Movements\Actions;

use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Illuminate\Contracts\Container\BindingResolutionException;

class GetTotalAmountOfDeletedMovementsByGroupAction
{
    private MovementRepositoryInterface $movementRepository;

    /**
     * Constructor
     *
     * @param MovementRepositoryInterface $movementRepository
     */
    public function __construct(MovementRepositoryInterface $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    /**
     * Execute the action to get the total amount of deleted movements for a group
     *
     * @param int $groupId
     * @return float
     */
    public function execute(int $groupId): float
    {
        $deletedMovements = $this->movementRepository->getDeletedMovementsByGroup($groupId);

        if ($deletedMovements->isEmpty())
            return 0.0;

        // Calcula o saldo final considerando todas as movimentações deletadas
        $totalBalance = 0.0;

        foreach ($deletedMovements as $movement) {
            $amount = $this->movementRepository->getMovementAmount($movement);

            if ($movement->type === EntryRepository::ENTRIES_VALUE || $movement->is_initial_balance)
                $totalBalance += $amount;

            else if ($movement->type === ExitRepository::EXIT_TYPE)
                $totalBalance -= $amount;
        }

        return $totalBalance;
    }
}
