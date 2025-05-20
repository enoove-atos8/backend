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
     * Calcula o saldo atual para um grupo específico
     *
     * @param int $groupId ID do grupo
     * @return float Saldo atual calculado
     */
    public function calculateCurrentBalance(int $groupId): float
    {
        $currentBalance = 0.0;

        $initialBalanceMovement = $this->movementRepository->getInitialMovementsByGroup($groupId);

        if ($initialBalanceMovement)
            $currentBalance = (float)$initialBalanceMovement->amount;

        $movements = $this->movementRepository->getMovementsByGroup($groupId);

        if (!$movements->isEmpty())
        {
            foreach ($movements as $movement)
            {
                $amount = (float)$movement->amount;

                if ($movement->type === EntryRepository::ENTRY_TYPE)
                    $currentBalance += $amount;

                else if ($movement->type === ExitRepository::EXIT_TYPE)
                    $currentBalance -= $amount;
            }
        }

        return $currentBalance;
    }

    /**
     * Execute the action.
     *
     * @param MovementsData $movementsData
     * @return mixed
     */
    public function execute(MovementsData $movementsData): mixed
    {
        if (!isset($movementsData->balance))
        {
            if (isset($movementsData->groupId) && $movementsData->groupId > 0)
                $movementsData->balance = $this->calculateCurrentBalance($movementsData->groupId);

            else
                $movementsData->balance = 0.0;
        }

        $currentBalance = $movementsData->balance;
        $amount = $movementsData->amount;


        if (!$movementsData->isInitialBalance)
        {
            $newBalance = $movementsData->type === EntryRepository::ENTRY_TYPE
                ? $currentBalance + $amount
                : ($movementsData->type === ExitRepository::EXIT_TYPE
                    ? $currentBalance - $amount
                    : $currentBalance);

            if($amount > $currentBalance) {
                $movementsData->balance = 0.0;
            }
            else {
                $movementsData->balance = $newBalance;
            }
        }

        return $this->movementRepository->createMovement($movementsData);
    }
}
