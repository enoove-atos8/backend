<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Financial\Exits\Exits\Constants\ReturnMessages;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Domain\Financial\Exits\Exits\Models\Exits;
use Domain\Financial\Movements\Actions\CreateMovementAction;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Infrastructure\Repositories\Financial\Movements\MovementRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CreateExitAction
{
    private ExitRepositoryInterface $exitRepository;
    private MovementRepositoryInterface $movementRepository;
    private MovementsData $movementsData;
    private CreateMovementAction $createMovementAction;

    public function __construct(
        ExitRepositoryInterface         $exitRepositoryInterface,
        MovementRepositoryInterface     $movementRepositoryInterface,
        MovementsData                   $movementsData,
        CreateMovementAction            $createMovementAction
    )
    {
        $this->exitRepository = $exitRepositoryInterface;
        $this->movementRepository = $movementRepositoryInterface;
        $this->movementsData = $movementsData;
        $this->createMovementAction = $createMovementAction;
    }


    /**
     * @param ExitData $exitData
     * @return Exits
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function execute(ExitData $exitData): Exits
    {
        $dateExitRegister = $exitData->dateExitRegister;
        $dateTransactionCompensation = $exitData->dateTransactionCompensation;

        if(!is_null($dateTransactionCompensation))
            if(substr($dateExitRegister, 0, 7) !== substr($dateTransactionCompensation, 0, 7))
                $exitData->dateExitRegister = substr($dateTransactionCompensation, 0, 7) . '-01';

        $exit = $this->exitRepository->newExit($exitData);
        $exitData->id = $exit->id;

        if(!is_null($exitData->id))
        {
            $movementData = $this->movementsData::fromObjectData(null, $exitData);
            $this->createMovementAction->execute($movementData);

            return $exit;
        }
        else
            throw new GeneralExceptions(ReturnMessages::CREATE_EXIT_ERROR, 500);
    }
}
