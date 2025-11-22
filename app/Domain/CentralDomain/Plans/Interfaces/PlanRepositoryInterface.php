<?php

namespace Domain\CentralDomain\Plans\Interfaces;

use Domain\CentralDomain\Plans\DataTransferObjects\PlanData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface PlanRepositoryInterface
{
    public function getPlans(): Collection;

    public function getPlanByName(string $name): ?Model;

    public function getPlanById(int $id): ?PlanData;
}
