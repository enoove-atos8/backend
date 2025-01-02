<?php

namespace Domain\CentralDomain\Plans\Interfaces;

use Domain\CentralDomain\Plans\DataTransferObjects\PlanData;
use Illuminate\Support\Collection;

interface PlanRepositoryInterface
{
    /**
     * @return Collection
     */
    public function getPlans(): Collection;
}
