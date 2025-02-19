<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Models\Church;
use Exception;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Church\ChurchRepository;
use Throwable;

class GetChurchesByPlanIdAction
{
    private ChurchRepository $churchRepository;

    public function __construct(
        ChurchRepositoryInterface  $churchRepositoryInterface,
    )
    {
        $this->churchRepository = $churchRepositoryInterface;
    }


    /**
     * @throws Throwable
     */
    public function execute($id): Collection
    {
        return $this->churchRepository->getChurchesByPlanId($id);
    }
}
