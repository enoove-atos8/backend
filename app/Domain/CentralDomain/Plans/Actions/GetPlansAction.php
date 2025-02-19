<?php

namespace App\Domain\CentralDomain\Plans\Actions;

use Domain\CentralDomain\Plans\Interfaces\PlanRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\CentralDomain\PlanRepository;

class GetPlansAction
{
    private PlanRepository $planRepository;

    public function __construct(PlanRepositoryInterface $planRepositoryInterface)
    {
        $this->planRepository = $planRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException|GeneralExceptions
     */
    public function execute(): Collection
    {
        $plans = $this->planRepository->getPlans();

        if(count($plans) > 0)
            return $plans;
        else
            throw new GeneralExceptions('Nenhum plano encontrado!!!', 500);
    }
}
