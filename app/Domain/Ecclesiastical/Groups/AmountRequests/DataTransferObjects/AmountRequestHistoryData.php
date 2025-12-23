<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AmountRequestHistoryData extends DataTransferObject
{
    public ?int $id;

    public ?int $amountRequestId;

    public ?string $event;

    public ?string $description;

    public ?int $userId;

    public ?string $userName;

    public ?array $metadata;

    public ?string $createdAt;

    public ?string $updatedAt;

    /**
     * Create an AmountRequestHistoryData instance from a database response array.
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['amount_request_history_id'] ?? null,
            amountRequestId: $data['amount_request_history_amount_request_id'] ?? null,
            event: $data['amount_request_history_event'] ?? null,
            description: $data['amount_request_history_description'] ?? null,
            userId: $data['amount_request_history_user_id'] ?? null,
            userName: $data['users_full_name'] ?? null,
            metadata: isset($data['amount_request_history_metadata']) ? json_decode($data['amount_request_history_metadata'], true) : null,
            createdAt: $data['amount_request_history_created_at'] ?? null,
            updatedAt: $data['amount_request_history_updated_at'] ?? null,
        );
    }
}
