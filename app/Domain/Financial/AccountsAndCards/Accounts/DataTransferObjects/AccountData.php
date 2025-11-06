<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountData extends DataTransferObject
{
    public ?int $id;

    public ?string $accountType;

    public ?string $bankName;

    public ?string $agencyNumber;

    public ?string $accountNumber;

    public ?float $initialBalance;

    public ?string $initialBalanceDate;

    public ?bool $activated;

    /**
     * Create a CardData instance from an array response.
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'accountType' => $data['account_type'] ?? null,
            'bankName' => $data['bank_name'] ?? null,
            'agencyNumber' => $data['agency_number'] ?? null,
            'accountNumber' => $data['account_number'] ?? null,
            'initialBalance' => isset($data['initial_balance']) ? (float) $data['initial_balance'] : null,
            'initialBalanceDate' => $data['initial_balance_date'] ?? null,
            'activated' => $data['activated'] ?? null,
        ]);
    }
}
