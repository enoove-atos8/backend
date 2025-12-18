<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators;

class RecentMovementData
{
    public function __construct(
        public int $id,
        public string $movementType,
        public string $movementDate,
        public string $transactionType,
        public string $description,
        public float $amount
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            movementType: $data['movement_type'],
            movementDate: $data['movement_date'],
            transactionType: $data['transaction_type'] ?? '',
            description: $data['description'] ?? '',
            amount: (float) $data['amount']
        );
    }
}
