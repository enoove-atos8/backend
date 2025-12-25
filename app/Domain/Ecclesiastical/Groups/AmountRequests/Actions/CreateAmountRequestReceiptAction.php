<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Constants\ReturnMessages;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestHistoryData;
use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReceiptData;
use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Services\External\minIO\MinioStorageService;

class CreateAmountRequestReceiptAction
{
    private AmountRequestRepositoryInterface $repository;

    private MinioStorageService $minioStorageService;

    public function __construct(
        AmountRequestRepositoryInterface $repository,
        MinioStorageService $minioStorageService
    ) {
        $this->repository = $repository;
        $this->minioStorageService = $minioStorageService;
    }

    /**
     * Create a receipt and update proven amount
     *
     * @throws GeneralExceptions
     */
    public function execute(AmountRequestReceiptData $data, UploadedFile $file, string $path): int
    {
        // Check if amount request exists
        $existing = $this->repository->getById($data->amountRequestId);
        if ($existing === null) {
            throw new GeneralExceptions(ReturnMessages::AMOUNT_REQUEST_NOT_FOUND, 404);
        }

        // Only transferred, partially_proven or overdue requests can receive receipts
        $validStatuses = [ReturnMessages::STATUS_TRANSFERRED, ReturnMessages::STATUS_PARTIALLY_PROVEN, ReturnMessages::STATUS_OVERDUE];
        \Log::info('DEBUG Receipt - Status: [' . $existing->status . '] | Type: ' . gettype($existing->status) . ' | Valid: ' . json_encode($validStatuses));
        if (! in_array($existing->status, $validStatuses)) {
            throw new GeneralExceptions(ReturnMessages::INVALID_STATUS_FOR_RECEIPT . ' Status atual: ' . $existing->status, 400);
        }

        // Upload file to MinIO
        $tenant = tenant('id');
        $uploadedUrl = $this->minioStorageService->upload($file, $path, $tenant);

        if (empty($uploadedUrl)) {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPLOAD_FILE, 500);
        }

        // Set the uploaded URL in the data
        $data->imageUrl = $uploadedUrl;

        // Create receipt
        $receiptId = $this->repository->createReceipt($data);

        if ($receiptId === 0) {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_RECEIPT, 500);
        }

        // Recalculate proven amount
        $this->recalculateProvenAmount($data->amountRequestId, $existing->requestedAmount);

        // Register history event
        $this->repository->createHistory(new AmountRequestHistoryData(
            amountRequestId: $data->amountRequestId,
            event: ReturnMessages::HISTORY_EVENT_RECEIPT_ADDED,
            description: ReturnMessages::HISTORY_DESCRIPTIONS[ReturnMessages::HISTORY_EVENT_RECEIPT_ADDED],
            userId: $data->createdBy,
            metadata: [
                'receipt_id' => $receiptId,
                'amount' => $data->amount,
            ]
        ));

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
