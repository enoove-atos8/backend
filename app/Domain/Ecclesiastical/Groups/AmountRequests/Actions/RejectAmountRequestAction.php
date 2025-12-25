<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class RejectAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Reject an amount request
     *
     * @throws GeneralExceptions
     */
    public function execute(int $id, int $rejectedBy, string $rejectionReason): bool
    {
        // Check if exists
        $existing = $this->repository->getById($id);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Only pending requests can be rejected
        if ($existing->status !== ReturnMessages::STATUS_PENDING) {
            throw new GeneralExceptions(ReturnMessages::INVALID_STATUS_FOR_REJECTION, 400);
        }

        // Rejection reason is required
        if (empty($rejectionReason)) {
            throw new GeneralExceptions(ReturnMessages::REJECTION_REASON_REQUIRED, 400);
        }

        $rejected = $this->repository->reject($id, $rejectedBy, $rejectionReason);

        if (! $rejected) {
            throw new GeneralExceptions(ReturnMessages::ERROR_REJECT_AMOUNT_REQUEST, 500);
        }

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $id,
            event: ReturnMessages::HISTORY_EVENT_REJECTED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_REJECTED],
            userId: $rejectedBy,
            metadata: [
                'rejection_reason' => $rejectionReason,
            ]
        ));

        return true;
    }
}
