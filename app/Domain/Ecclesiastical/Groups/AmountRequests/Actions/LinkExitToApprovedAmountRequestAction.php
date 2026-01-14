<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
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
    public function execute(int $amountRequestId, int $exitId, int $userId, string $requestedAmount): bool
    {
        // Link the exit to the amount request
        $linked = $this->repository->linkExit($amountRequestId, $exitId);

        if (! $linked) {
            return false;
        }

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $amountRequestId,
            event: ReturnMessages::HISTORY_EVENT_TRANSFERRED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_TRANSFERRED],
            userId: $userId,
            metadata: [
                ReturnMessages::METADATA_KEY_EXIT_ID => $exitId,
                ReturnMessages::METADATA_KEY_REQUESTED_AMOUNT => $requestedAmount,
            ]
        ));

        return true;
    }
}
