<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Financial\Exits\Exits\Constants\ReturnMessages;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Domain\Financial\Movements\Actions\DeleteMovementByExitId;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class DeleteExitAction
{
    private ExitRepositoryInterface $exitRepository;

    private DeleteMovementByExitId $deleteMovementByExitId;

    public function __construct(
        ExitRepositoryInterface $exitRepositoryInterface,
        DeleteMovementByExitId $deleteMovementByExitId
    ) {
        $this->exitRepository = $exitRepositoryInterface;
        $this->deleteMovementByExitId = $deleteMovementByExitId;
    }

    /**
     * @throws BindingResolutionException
     * @throws GeneralExceptions|Throwable
     */
    public function execute($id): bool
    {
        $exitDeleted = $this->exitRepository->deleteExit($id);

        if ($exitDeleted) {
            $this->deleteMovementByExitId->execute($id);

            return true;
        } else {
            throw new GeneralExceptions(ReturnMessages::EXIT_DELETE_ERROR, 500);
        }
    }
}
