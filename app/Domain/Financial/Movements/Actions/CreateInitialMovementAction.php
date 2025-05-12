<?php

namespace Domain\Financial\Movements\Actions;

use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CreateInitialMovementAction
{
    private GetTotalAmountOfDeletedMovementsByGroupAction $getTotalAmountOfDeletedMovementsByGroupAction;
    private MovementsData $movementsData;
    private CreateMovementAction $createMovementAction;
    private DeleteMovementsOfGroupAction $deleteMovementsOfGroupAction;
    private CheckGroupMovementsWithoutInitialAction $checkGroupMovementsWithoutInitialAction;

    /**
     * Constructor
     *
     * @param GetTotalAmountOfDeletedMovementsByGroupAction $getTotalAmountOfDeletedMovementsByGroupAction
     * @param MovementsData $movementsData
     * @param CreateMovementAction $createMovementAction
     * @param DeleteMovementsOfGroupAction $deleteMovementsOfGroupAction
     * @param CheckGroupMovementsWithoutInitialAction $checkGroupMovementsWithoutInitialAction
     */
    public function __construct(
        GetTotalAmountOfDeletedMovementsByGroupAction $getTotalAmountOfDeletedMovementsByGroupAction,
        MovementsData $movementsData,
        CreateMovementAction $createMovementAction,
        DeleteMovementsOfGroupAction $deleteMovementsOfGroupAction,
        CheckGroupMovementsWithoutInitialAction $checkGroupMovementsWithoutInitialAction
    ) {
        $this->getTotalAmountOfDeletedMovementsByGroupAction = $getTotalAmountOfDeletedMovementsByGroupAction;
        $this->movementsData = $movementsData;
        $this->createMovementAction = $createMovementAction;
        $this->deleteMovementsOfGroupAction = $deleteMovementsOfGroupAction;
        $this->checkGroupMovementsWithoutInitialAction = $checkGroupMovementsWithoutInitialAction;
    }

    /**
     * Execute the action to create an initial movement for a group, considering deleted movements
     *
     * @param MovementsData $movementsData
     * @return mixed
     * @throws UnknownProperties|GeneralExceptions
     */
    public function execute(MovementsData $movementsData): mixed
    {
        $movementsWithoutInitial = $this->checkGroupMovementsWithoutInitialAction->execute($movementsData->groupId);

        if ($movementsWithoutInitial)
            $this->deleteMovementsOfGroupAction->execute($movementsData->groupId);

        $movementData = $this->movementsData::fromSelf(
            $movementsData,
            true
        );

        return $this->createMovementAction->execute($movementData);
    }
}
