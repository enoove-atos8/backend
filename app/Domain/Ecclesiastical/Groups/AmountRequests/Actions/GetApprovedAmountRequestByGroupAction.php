<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;

class GetApprovedAmountRequestByGroupAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get an approved amount request by group ID and type
     */
    public function execute(int $groupId, string $type): ?AmountRequestData
    {
        return $this->repository->getApprovedByGroupId($groupId, $type);
    }
}
