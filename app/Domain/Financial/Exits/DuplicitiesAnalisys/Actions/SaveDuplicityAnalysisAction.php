<?php

namespace Domain\Financial\Exits\DuplicitiesAnalisys\Actions;

use Domain\Financial\Exits\Exits\Actions\DeleteExitAction;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class SaveDuplicityAnalysisAction
{
    private ExitRepositoryInterface $exitRepository;
    private DeleteExitAction $deleteExitAction;


    public function __construct(
        ExitRepositoryInterface $exitRepositoryInterface,
        DeleteExitAction $deleteExitAction
    )
    {
        $this->exitRepository = $exitRepositoryInterface;
        $this->deleteExitAction = $deleteExitAction;
    }


    /**
     * @param array $exits
     * @return void
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function execute(array $exits): void
    {
        if(array_key_exists('kept', $exits))
        {
            if(count($exits['kept']) > 0)
            {
                foreach ($exits['kept'] as $exit)
                    $this->exitRepository->setDuplicityAnalysis($exit);
            }
        }

        if(array_key_exists('excluded', $exits))
        {
            if(count($exits['excluded']) > 0)
            {
                foreach ($exits['excluded'] as $exit)
                {
                    $this->exitRepository->setDuplicityAnalysis($exit);
                    $this->deleteExitAction->execute($exit);
                }
            }
        }
    }
}
