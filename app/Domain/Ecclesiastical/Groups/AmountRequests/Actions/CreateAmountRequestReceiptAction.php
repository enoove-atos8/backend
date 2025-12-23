<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReceiptData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateAmountRequestReceiptAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a receipt and update proven amount
     *
     * @throws GeneralExceptions
     */
    public function execute(AmountRequestReceiptData $data): int
    {
        // Check if amount request exists
        $existing = $this->repository->getById($data->amountRequestId);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Only transferred, partially_proven or overdue requests can receive receipts
        $validStatuses = [ReturnMessages::STATUS_TRANSFERRED, ReturnMessages::STATUS_PARTIALLY_PROVEN, ReturnMessages::STATUS_OVERDUE];
        if (! in_array($existing->status, $validStatuses)) {
            throw new GeneralExceptions(ReturnMessages::INVALID_STATUS_FOR_RECEIPT, 400);
        }

        // Create receipt
        $receiptId = $this->repository->createReceipt($data);

        if ($receiptId === 0) {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_RECEIPT, 500);
        }

        // Recalculate proven amount
        $this->recalculateProvenAmount($data->amountRequestId, $existing->requestedAmount);

        return $receiptId;
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
