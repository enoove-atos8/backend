<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Financial\Exits\Exits\Constants\ReturnMessages;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Domain\Financial\Exits\Exits\Models\Exits;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

class CreateExitAction
{
    private ExitRepository $exitRepository;

    public function __construct(ExitRepositoryInterface $exitRepositoryInterface)
    {
        $this->exitRepository = $exitRepositoryInterface;
    }


    /**
     * @param ExitData $exitData
     * @return Exits
     * @throws GeneralExceptions
     */
    public function execute(ExitData $exitData): Exits
    {
        $dateExitRegister = $exitData->dateExitRegister;
        $dateTransactionCompensation = $exitData->dateTransactionCompensation;

        if(!is_null($dateTransactionCompensation))
            if(substr($dateExitRegister, 0, 7) !== substr($dateTransactionCompensation, 0, 7))
                $exitData->dateExitRegister = substr($dateTransactionCompensation, 0, 7) . '-01';

        $exit = $this->exitRepository->newExit($exitData);

        if(!is_null($exit->id))
            return $exit;
        else
            throw new GeneralExceptions(ReturnMessages::CREATE_EXIT_ERROR, 500);

    }
}
