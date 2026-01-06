<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MovementsData extends DataTransferObject
{
    // Property names for Collection filtering
    const PROPERTY_MOVEMENT_TYPE = 'movementType';

    const PROPERTY_CONCILIATED_STATUS = 'conciliatedStatus';

    const PROPERTY_AMOUNT = 'amount';

    public int $id;

    public int $accountId;

    public ?int $fileId;

    public string $movementDate;

    public string $transactionType;

    public string $description;

    public float $amount;

    public string $movementType;

    public bool $anonymous;

    public string $conciliatedStatus;

    public string $createdAt;

    public string $updatedAt;

    /**
     * Create a MovementsData instance from an array response.
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['accounts_movements_id'] ?? null,
            accountId: $data['accounts_movements_account_id'] ?? null,
            fileId: $data['accounts_movements_file_id'] ?? null,
            movementDate: $data['accounts_movements_movement_date'] ?? null,
            transactionType: $data['accounts_movements_transaction_type'] ?? null,
            description: $data['accounts_movements_description'] ?? null,
            amount: $data['accounts_movements_amount'] ?? null,
            movementType: $data['accounts_movements_movement_type'] ?? null,
            anonymous: $data['accounts_movements_anonymous'] ?? null,
            conciliatedStatus: $data['accounts_movements_conciliated_status'] ?? null,
            createdAt: $data['accounts_movements_created_at'] ?? null,
            updatedAt: $data['accounts_movements_updated_at'] ?? null,
        );
    }
}
