<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MovementsData extends DataTransferObject
{
    /** @var int */
    public int $id;

    /** @var int */
    public int $accountId;

    /** @var int|null */
    public ?int $fileId;

    /** @var string */
    public string $movementDate;

    /** @var string */
    public string $transactionType;

    /** @var string */
    public string $description;

    /** @var float */
    public float $amount;

    /** @var string */
    public string $movementType;

    /** @var bool */
    public bool $anonymous;

    /** @var string */
    public string $conciliatedStatus;

    /** @var string */
    public string $createdAt;

    /** @var string */
    public string $updatedAt;


    /**
     * Create a MovementsData instance from an array response.
     *
     * @param array $data
     * @return self
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
