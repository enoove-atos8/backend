<?php

namespace Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class AccountMovementsData extends DataTransferObject
{
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
}
