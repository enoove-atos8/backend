<?php

namespace App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountBalanceData extends DataTransferObject
{
    public ?int $id;

    public ?int $accountId;

    public ?string $referenceDate;

    public ?float $previousMonthBalance;

    public ?float $currentMonthBalance;

    public ?bool $isInitialBalance;

    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            accountId: $data['account_id'] ?? null,
            referenceDate: $data['reference_date'] ?? null,
            previousMonthBalance: isset($data['previous_month_balance']) ? (float) $data['previous_month_balance'] : null,
            currentMonthBalance: isset($data['current_month_balance']) ? (float) $data['current_month_balance'] : null,
            isInitialBalance: $data['is_initial_balance'] ?? false,
        );
    }
}
