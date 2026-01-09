<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Application\Core\Events\Ecclesiastical\Groups\AmountRequests\AmountRequestStatusChanged;
use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class LinkExitToAmountRequestAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Link, update or unlink an exit to an amount request
     *
     * @param  int  $id  Amount request ID
     * @param  int|null  $exitId  Exit ID to link/update, or null to unlink
     * @param  int  $userId  User performing the action
     *
     * @throws GeneralExceptions
     */
    public function execute(int $id, ?int $exitId, int $userId): bool
    {
        // Check if amount request exists
        $existing = $this->repository->getById($id);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Linking or updating exit (exitId provided)
        if ($exitId !== null) {
            // Approved requests can be linked to an exit
            // Transferred requests can have their exit updated (changed to another)
            $validStatuses = [ReturnMessages::STATUS_APPROVED, ReturnMessages::STATUS_TRANSFERRED];
            if (! in_array($existing->status, $validStatuses)) {
                throw new GeneralExceptions(ReturnMessages::INVALID_STATUS_FOR_LINK, 400);
            }

            $oldStatus = $existing->status;

            $updated = $this->repository->linkExit($id, $exitId);

            if (! $updated) {
                throw new GeneralExceptions(ReturnMessages::ERROR_LINK_EXIT, 500);
            }

            // Register history event
            $this->repository->createHistory(new AmountRequestHistoryData(
                amountRequestId: $id,
                event: ReturnMessages::HISTORY_EVENT_TRANSFERRED,
                description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_TRANSFERRED],
                userId: $userId,
                metadata: [
                    ReturnMessages::METADATA_KEY_EXIT_ID => $exitId,
                    ReturnMessages::METADATA_KEY_REQUESTED_AMOUNT => $existing->requestedAmount,
                ]
            ));

            // Dispatch Event para notificação WhatsApp (apenas se mudou para transferred)
            if ($oldStatus === ReturnMessages::STATUS_APPROVED) {
                event(new AmountRequestStatusChanged(
                    amountRequestId: $id,
                    oldStatus: $oldStatus,
                    newStatus: ReturnMessages::STATUS_TRANSFERRED,
                    userId: $userId,
                    additionalData: [
                        'exit_id' => $exitId,
                        'requested_amount' => $existing->requestedAmount,
                        'proof_deadline' => $existing->proofDeadline,
                    ]
                ));
            }

            return true;
        }

        // Unlinking (exitId is null)
        // Only transferred requests can be unlinked
        if ($existing->status !== ReturnMessages::STATUS_TRANSFERRED) {
            throw new GeneralExceptions(ReturnMessages::INVALID_STATUS_FOR_UNLINK, 400);
        }

        $updated = $this->repository->unlinkExit($id);

        if (! $updated) {
            throw new GeneralExceptions(ReturnMessages::ERROR_UNLINK_EXIT, 500);
        }

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $id,
            event: ReturnMessages::HISTORY_EVENT_EXIT_UNLINKED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_EXIT_UNLINKED],
            userId: $userId,
            metadata: [
                'previous_exit_id' => $existing->transferExitId,
            ]
        ));

        return true;
    }
}
