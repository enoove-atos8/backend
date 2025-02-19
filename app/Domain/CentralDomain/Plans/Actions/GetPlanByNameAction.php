<?php

namespace Domain\CentralDomain\Plans\Actions;

use Domain\CentralDomain\Plans\Interfaces\PlanRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PlanRepository;

class GetPlanByNameAction
{
    private PlanRepository $planRepository;

    public function __construct(PlanRepositoryInterface $planRepositoryInterface)
    {
        $this->planRepository = $planRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     */
    public function execute(string $name): Model | null
    {
        $plan = $this->planRepository->getPlanByName($name);

        if($plan)
            return $plan;
        else
            return null;
    }
}
