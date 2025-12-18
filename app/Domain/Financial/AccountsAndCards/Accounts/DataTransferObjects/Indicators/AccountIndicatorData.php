<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators;

class AccountIndicatorData
{
    public function __construct(
        public int $accountId,
        public string $bankName,
        public string $accountType,
        public float $currentBalance,
        public ?string $lastMovementDate
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            accountId: (int) $data['account_id'],
            bankName: $data['bank_name'],
            accountType: $data['account_type'],
            currentBalance: (float) ($data['current_balance'] ?? 0),
            lastMovementDate: $data['last_movement_date'] ?? null
        );
    }
}
