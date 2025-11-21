<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class GetChurchesAction
{
    private ChurchRepositoryInterface $churchRepository;

    public function __construct(
        ChurchRepositoryInterface  $churchRepositoryInterface,
    )
    {
        $this->churchRepository = $churchRepositoryInterface;
    }


    /**
     * @throws Throwable
     */
    public function execute(): Collection
    {
        $churches = $this->churchRepository->getChurches();

        if(count($churches) > 0)
        {
            return $churches;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::NOT_FOUND_CHURCHES, 404);
        }
    }
}
