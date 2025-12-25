<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class CloseAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Close an amount request manually
     *
     * @throws GeneralExceptions
     */
    public function execute(int $id, int $closedBy): bool
    {
        // Check if exists
        $existing = $this->repository->getById($id);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Only proven requests can be closed (all amount was proven)
        // Or requests with devolution already linked (proven + devolution = requested)
        $validStatuses = [ReturnMessages::STATUS_PROVEN];

        // Allow closing partially_proven or overdue if devolution is already linked
        if ($existing->devolutionEntryId !== null) {
            $validStatuses[] = ReturnMessages::STATUS_PARTIALLY_PROVEN;
            $validStatuses[] = ReturnMessages::STATUS_OVERDUE;
        }

        if (! in_array($existing->status, $validStatuses)) {
            throw new GeneralExceptions(ReturnMessages::INVALID_STATUS_FOR_CLOSE, 400);
        }

        $requestedAmount = (float) $existing->requestedAmount;
        $provenAmount = (float) $existing->provenAmount;
        $devolutionAmount = (float) ($existing->devolutionAmount ?? 0);

        // Close the request
        $closed = $this->repository->close($id, $closedBy);

        if (! $closed) {
            throw new GeneralExceptions(ReturnMessages::ERROR_CLOSE_AMOUNT_REQUEST, 500);
        }

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $id,
            event: ReturnMessages::HISTORY_EVENT_CLOSED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_CLOSED],
            userId: $closedBy,
            metadata: [
                'requested_amount' => $requestedAmount,
                'proven_amount' => $provenAmount,
                'devolution_amount' => $devolutionAmount,
            ]
        ));

        return true;
    }
}
