<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AmountRequestReminderData extends DataTransferObject
{
    public ?int $id;

    public ?int $amountRequestId;

    public ?string $type;

    public ?string $channel;

    public ?string $scheduledAt;

    public ?string $sentAt;

    public ?string $status;

    public ?string $errorMessage;

    public ?array $metadata;

    public ?string $createdAt;

    public ?string $updatedAt;

    /**
     * Create an AmountRequestReminderData instance from a database response array.
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['amount_request_reminders_id'] ?? null,
            amountRequestId: $data['amount_request_reminders_amount_request_id'] ?? null,
            type: $data['amount_request_reminders_type'] ?? null,
            channel: $data['amount_request_reminders_channel'] ?? null,
            scheduledAt: $data['amount_request_reminders_scheduled_at'] ?? null,
            sentAt: $data['amount_request_reminders_sent_at'] ?? null,
            status: $data['amount_request_reminders_status'] ?? null,
            errorMessage: $data['amount_request_reminders_error_message'] ?? null,
            metadata: isset($data['amount_request_reminders_metadata'])
                ? json_decode($data['amount_request_reminders_metadata'], true)
                : null,
            createdAt: $data['amount_request_reminders_created_at'] ?? null,
            updatedAt: $data['amount_request_reminders_updated_at'] ?? null,
        );
    }

    /**
     * Create an AmountRequestReminderData instance from an Eloquent model array.
     *
     * @throws UnknownProperties
     */
    public static function fromSelf(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'amountRequestId' => $data['amount_request_id'] ?? null,
            'type' => $data['type'] ?? null,
            'channel' => $data['channel'] ?? null,
            'scheduledAt' => $data['scheduled_at'] ?? null,
            'sentAt' => $data['sent_at'] ?? null,
            'status' => $data['status'] ?? null,
            'errorMessage' => $data['error_message'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'createdAt' => $data['created_at'] ?? null,
            'updatedAt' => $data['updated_at'] ?? null,
        ]);
    }
}
