<?php

namespace Domain\CentralDomain\Churches\Church\Actions;

use App\Infrastructure\Repositories\CentralDomain\Church\ChurchRepository;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Illuminate\Support\Collection;
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
