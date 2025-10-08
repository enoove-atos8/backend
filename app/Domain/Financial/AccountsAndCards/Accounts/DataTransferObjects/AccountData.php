<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountData extends DataTransferObject
{
    /** @var int|null */
    public ?int $id;

    /** @var string|null */
    public ?string $accountType;

    /** @var string|null */
    public ?string $bankName;

    /** @var string|null */
    public ?string $agencyNumber;

    /** @var string|null */
    public ?string $accountNumber;

    /** @var boolean|null */
    public ?bool $activated;


    /**
     * Create a CardData instance from an array response.
     *
     * @param array $data
     * @return self
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
            'activated' => $data['activated'] ?? null,
        ]);
    }
}
