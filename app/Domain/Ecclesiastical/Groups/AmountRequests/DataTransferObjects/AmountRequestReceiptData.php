<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AmountRequestReceiptData extends DataTransferObject
{
    public ?int $id;

    public ?int $amountRequestId;

    public ?string $amount;

    public ?string $description;

    public ?string $imageUrl;

    public ?string $receiptDate;

    public ?int $createdBy;

    public ?string $createdAt;

    public ?string $updatedAt;

    public ?bool $deleted;

    /**
     * Create an AmountRequestReceiptData instance from a database response array.
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['amount_request_receipts_id'] ?? null,
            amountRequestId: $data['amount_request_receipts_amount_request_id'] ?? null,
            amount: $data['amount_request_receipts_amount'] ?? null,
            description: $data['amount_request_receipts_description'] ?? null,
            imageUrl: $data['amount_request_receipts_image_url'] ?? null,
            receiptDate: $data['amount_request_receipts_receipt_date'] ?? null,
            createdBy: $data['amount_request_receipts_created_by'] ?? null,
            createdAt: $data['amount_request_receipts_created_at'] ?? null,
            updatedAt: $data['amount_request_receipts_updated_at'] ?? null,
            deleted: isset($data['amount_request_receipts_deleted']) ? (bool) $data['amount_request_receipts_deleted'] : null,
        );
    }

    /**
     * Create an AmountRequestReceiptData instance from an Eloquent model array.
     *
     * @throws UnknownProperties
     */
    public static function fromSelf(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'amountRequestId' => $data['amount_request_id'] ?? null,
            'amount' => $data['amount'] ?? null,
            'description' => $data['description'] ?? null,
            'imageUrl' => $data['image_url'] ?? null,
            'receiptDate' => $data['receipt_date'] ?? null,
            'createdBy' => $data['created_by'] ?? null,
            'createdAt' => $data['created_at'] ?? null,
            'updatedAt' => $data['updated_at'] ?? null,
            'deleted' => $data['deleted'] ?? null,
        ]);
    }
}
