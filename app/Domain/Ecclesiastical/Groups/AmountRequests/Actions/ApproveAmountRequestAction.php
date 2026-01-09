<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Application\Core\Events\Ecclesiastical\Groups\AmountRequests\AmountRequestStatusChanged;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class ApproveAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Approve an amount request
     *
     * @throws GeneralExceptions
     */
    public function execute(int $id, int $approvedBy): bool
    {
        // Check if exists
        $existing = $this->repository->getById($id);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Only pending requests can be approved
        if ($existing->status !== ReturnMessages::STATUS_PENDING) {
            throw new GeneralExceptions(ReturnMessages::INVALID_STATUS_FOR_APPROVAL, 400);
        }

        $oldStatus = $existing->status;

        $approved = $this->repository->approve($id, $approvedBy);

        if (! $approved) {
            throw new GeneralExceptions(ReturnMessages::ERROR_APPROVE_AMOUNT_REQUEST, 500);
        }

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $id,
            event: ReturnMessages::HISTORY_EVENT_APPROVED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_APPROVED],
            userId: $approvedBy,
            metadata: [
                ReturnMessages::METADATA_KEY_REQUESTED_AMOUNT => $existing->requestedAmount,
            ]
        ));

        // Dispatch Event para notificação WhatsApp
        event(new AmountRequestStatusChanged(
            amountRequestId: $id,
            oldStatus: $oldStatus,
            newStatus: ReturnMessages::STATUS_APPROVED,
            userId: $approvedBy,
            additionalData: [
                'requested_amount' => $existing->requestedAmount,
                'proof_deadline' => $existing->proofDeadline,
            ]
        ));

        return true;
    }
}
