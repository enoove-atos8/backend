<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Financial\Exits\Exits\Constants\ReturnMessages;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

class DeleteExitAction
{
    private ExitRepositoryInterface $exitRepository;

    public function __construct(
        ExitRepositoryInterface $exitRepositoryInterface
    )
    {
        $this->exitRepository = $exitRepositoryInterface;
    }


    /**
     * @param $id
     * @return mixed
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function execute($id): mixed
    {
        $exit = $this->exitRepository->deleteExit($id);

        if($exit)
            return $exit;
        else
            throw new GeneralExceptions(ReturnMessages::EXIT_DELETE_ERROR, 500);

    }
}
