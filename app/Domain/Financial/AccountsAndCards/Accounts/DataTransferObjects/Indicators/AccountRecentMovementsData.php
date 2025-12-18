<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\Indicators;

use Illuminate\Support\Collection;

class AccountRecentMovementsData
{
    public function __construct(
        public int $accountId,
        public string $bankName,
        public string $accountType,
        public Collection $recentMovements
    ) {}

    public static function fromResponse(array $accountData, Collection $movements): self
    {
        return new self(
            accountId: (int) $accountData['id'],
            bankName: $accountData['bank_name'],
            accountType: $accountData['account_type'],
            recentMovements: $movements
        );
    }
}
