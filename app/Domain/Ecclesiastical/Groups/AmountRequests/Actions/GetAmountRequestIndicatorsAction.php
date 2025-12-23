<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestIndicatorsData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;

class GetAmountRequestIndicatorsAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get indicators/summary for dashboard
     *
     * @param  int|null  $groupId  Optional group filter
     */
    public function execute(?int $groupId = null): AmountRequestIndicatorsData
    {
        return $this->repository->getIndicators($groupId);
    }
}
