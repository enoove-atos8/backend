<?php

namespace App\Domain\CentralDomain\Billing\Actions;

use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Plans\DataTransferObjects\PlanData;
use Infrastructure\Exceptions\GeneralExceptions;

class GetChurchPlanAction
{
    public function __construct(
        private ChurchRepositoryInterface $churchRepository
    ) {
    }

    /**
     * @param int $churchId
     * @return PlanData|null
     */
    public function execute(int $churchId): PlanData|null
    {
        return $this->churchRepository->getChurchPlan($churchId);
    }
}
