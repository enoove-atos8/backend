<?php

namespace Domain\Financial\Movements\Actions;

use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Movements\Constants\ReturnMessages;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Domain\Financial\Movements\Models\Movement;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

class GetMovementsIndicatorsAction
{
    /**
     * @var MovementRepositoryInterface
     */
    private MovementRepositoryInterface $movementRepository;

    /**
     * GetMovementsIndicatorsAction constructor.
     *
     * @param MovementRepositoryInterface $movementRepository
     */
    public function __construct(MovementRepositoryInterface $movementRepository)
    {
        $this->movementRepository = $movementRepository;
    }

    /**
     * Execute the action to get movement indicators by group
     *
     * @param int $groupId
     * @param string $dates
     * @return array
     * @throws GeneralExceptions
     */
    public function execute(int $groupId, string $dates): array
    {
        // Get the initial balance for the group
        $initialBalance = self::getInitialBalance($this->movementRepository, $groupId);

        // Get movements data from repository
        $movements = $this->movementRepository->getMovementsIndicatorsByGroup($groupId, $dates);

        if ($movements->isEmpty()) {
            throw new GeneralExceptions(ReturnMessages::MOVEMENTS_NOT_FOUND, 404);
        }

        // Calculate entries and exits
        $financialSummary = self::calculateFinancialSummary($movements);

        // Calculate current balance
        $currentBalance = self::getCurrentBalance(
            $this->movementRepository,
            $groupId
        );


        return [
            'indicators'    => [
                'entries' => $financialSummary['entries'],
                'exits' => $financialSummary['exits'],
                'currentBalance' => $currentBalance
            ]
        ];
    }

    /**
     * Get initial balance for a group
     *
     * @param MovementRepositoryInterface $repository
     * @param int $groupId
     * @return float
     */
    private static function getInitialBalance(MovementRepositoryInterface $repository, int $groupId): float
    {
        $initialBalanceMovement = $repository->getInitialMovementsByGroup($groupId);

        if ($initialBalanceMovement && $initialBalanceMovement->isInitialBalance == 1)
            return (float)$initialBalanceMovement->amount;

        return 0.0;
    }

    /**
     * Calculate entries and exits from movements collection
     *
     * @param Collection $movements
     * @return array
     */
    private static function calculateFinancialSummary(Collection $movements): array
    {
        $entries = 0;
        $exits = 0;

        foreach ($movements as $movement) {

            if ($movement->type === EntryRepository::ENTRY_TYPE)
                $entries += (float)$movement->amount;

            elseif ($movement->type === ExitRepository::EXIT_TYPE)
                $exits += abs((float)$movement->amount);
        }

        return [
            'entries' => $entries,
            'exits' => $exits
        ];
    }

    /**
     * Get the current balance for a group
     *
     * - If there is an initial balance, start the sum from it.
     * - Otherwise, sum up all the 'amount' values directly.
     *
     * @param MovementRepositoryInterface $repository
     * @param int $groupId
     * @return float
     * @throws GeneralExceptions
     */
    public static function getCurrentBalance(MovementRepositoryInterface $repository, int $groupId): float
    {
        // Get the initial balance for the group
        $initialBalance = self::getInitialBalance($repository, $groupId);

        // Retrieve all movements for the group
        $movements = $repository->getMovementsIndicatorsByGroup($groupId, null); // No filters

        if ($movements->isEmpty()) {
            throw new GeneralExceptions(ReturnMessages::MOVEMENTS_NOT_FOUND, 404);
        }

        // Calculate the total sum of all amounts
        $finalBalance = $movements->reduce(
            function ($carry, $movement) {
                if ($movement->type === EntryRepository::ENTRY_TYPE) {
                    return $carry + (float) $movement->amount;
                } elseif ($movement->type === ExitRepository::EXIT_TYPE) {
                    return $carry - (float) $movement->amount;
                }
                return $carry;
            },
            $initialBalance // Se não existir initialBalance, considera 0
        );

        // If initial balance exists, start from it; otherwise, return sum of amounts
        return $finalBalance;
    }
}
