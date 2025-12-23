<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetAmountRequestHistoryAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get history/timeline for an amount request
     *
     * @throws GeneralExceptions
     */
    public function execute(int $amountRequestId): Collection
    {
        // Check if amount request exists
        $existing = $this->repository->getById($amountRequestId);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        return $this->repository->getHistory($amountRequestId);
    }
}
