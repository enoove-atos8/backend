<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;

class LinkExitToApprovedAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Link an exit to an approved amount request (for auto-linking)
     */
    public function execute(int $amountRequestId, int $exitId): bool
    {
        return $this->repository->linkExit($amountRequestId, $exitId);
    }
}
