<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Financial\Exits\Exits\Constants\ReturnMessages;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

class UpdateReceiptLinkAction
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
     * @param string $link
     * @return mixed
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function execute(int $exitId, string $link): mixed
    {
        $exit = $this->exitRepository->updateReceiptLink($exitId, $link);

        if($exit)
            return $exit;
        else
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_TIMESTAMP, 500);

    }
}
