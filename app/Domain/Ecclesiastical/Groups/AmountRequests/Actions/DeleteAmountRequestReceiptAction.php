<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class DeleteAmountRequestReceiptAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Delete a receipt and update proven amount
     *
     * @throws GeneralExceptions
     */
    public function execute(int $amountRequestId, int $receiptId, int $userId): bool
    {
        // Check if amount request exists
        $existing = $this->repository->getById($amountRequestId);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Only transferred, partially_proven or proven requests can have receipts deleted
        $validStatuses = [ReturnMessages::STATUS_TRANSFERRED, ReturnMessages::STATUS_PARTIALLY_PROVEN, ReturnMessages::STATUS_PROVEN, ReturnMessages::STATUS_OVERDUE];
        if (! in_array($existing->status, $validStatuses)) {
            throw new GeneralExceptions('Não é possível remover comprovantes neste status!', 400);
        }

        // Get receipt data before deletion for history
        $receipt = $this->repository->getReceiptById($amountRequestId, $receiptId);

        // Delete receipt
        $deleted = $this->repository->deleteReceipt($amountRequestId, $receiptId);

        if (! $deleted) {
            throw new GeneralExceptions(ReturnMessages::RECEIPT_NOT_FOUND, 404);
        }

        // Recalculate proven amount
        $this->recalculateProvenAmount($amountRequestId, $existing->requestedAmount);

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $amountRequestId,
            event: ReturnMessages::HISTORY_EVENT_RECEIPT_DELETED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_RECEIPT_DELETED],
            userId: $userId,
            metadata: [
                'receipt_id' => $receiptId,
                'amount' => $receipt?->amount,
            ]
        ));

        return true;
    }

    /**
     * Recalculate proven amount and update status
     */
    private function recalculateProvenAmount(int $amountRequestId, string $requestedAmount): void
    {
        $provenAmount = $this->repository->calculateProvenAmount($amountRequestId);
        $requested = (float) $requestedAmount;
        $proven = (float) $provenAmount;

        // Determine new status
        if ($proven >= $requested) {
            $status = ReturnMessages::STATUS_PROVEN;
        } elseif ($proven > 0) {
            $status = ReturnMessages::STATUS_PARTIALLY_PROVEN;
        } else {
            $status = ReturnMessages::STATUS_TRANSFERRED;
        }

        $this->repository->updateProvenAmount($amountRequestId, $provenAmount, $status);
    }
}
