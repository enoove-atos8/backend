<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Financial\Exits\Exits\Constants\ReturnMessages;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

class UpdateTimestampAction
{
    private ExitRepository $exitRepository;

    public function __construct(
        ExitRepositoryInterface $exitRepositoryInterface
    )
    {
        $this->exitRepository = $exitRepositoryInterface;
    }


    /**
     * @param int $exitId
     * @param string $timestamp
     * @return mixed
     * @throws GeneralExceptions
     * @throws BindingResolutionException
     */
    public function execute(int $exitId, string $timestamp): mixed
    {
        $exit = $this->exitRepository->updateTimestamp($exitId, $timestamp);

        if($exit)
            return $exit;
        else
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_TIMESTAMP, 500);

    }
}
