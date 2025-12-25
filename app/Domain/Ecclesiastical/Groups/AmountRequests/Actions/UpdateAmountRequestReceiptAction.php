<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReceiptData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateAmountRequestReceiptAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Update a receipt and recalculate proven amount
     *
     * @throws GeneralExceptions
     */
    public function execute(int $amountRequestId, int $receiptId, AmountRequestReceiptData $data, int $userId): bool
    {
        // Check if amount request exists
        $amountRequest = $this->repository->getById($amountRequestId);
        if ($amountRequest === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Check if receipt exists
        $receipt = $this->repository->getReceiptById($amountRequestId, $receiptId);
        if ($receipt === null) {
            throw new GeneralExceptions(ReturnMessages::RECEIPT_NOT_FOUND, 404);
        }

        // Update receipt
        $updated = $this->repository->updateReceipt($amountRequestId, $receiptId, $data);

        if (! $updated) {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_RECEIPT, 500);
        }

        // Recalculate proven amount
        $this->recalculateProvenAmount($amountRequestId, $amountRequest->requestedAmount);

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $amountRequestId,
            event: ReturnMessages::HISTORY_EVENT_RECEIPT_UPDATED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_RECEIPT_UPDATED],
            userId: $userId,
            metadata: [
                'receipt_id' => $receiptId,
                'previous_amount' => $receipt->amount,
                'new_amount' => $data->amount,
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
