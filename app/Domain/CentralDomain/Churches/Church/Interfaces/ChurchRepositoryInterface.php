<?php

namespace Domain\CentralDomain\Churches\Church\Interfaces;

use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
use Domain\CentralDomain\Churches\Church\Models\Church;
use Domain\CentralDomain\Plans\DataTransferObjects\PlanData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ChurchRepositoryInterface
{
    public function newChurch(ChurchData $churchData, string $awsS3Bucket): Church;

    public function getChurch(string $tenantId): ?ChurchData;

    public function getChurchById(int $churchId): ?ChurchData;

    public function getChurches(): Collection;

    public function getChurchesByPlanId(int $id): Collection;

    public function getChurchPlan(int $churchId): ?PlanData;
}
