<?php

namespace Domain\Entries\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class EntryData extends DataTransferObject
{
    /** @var string  */
    public string $entryType;

    /** @var string  */
    public string $transactionType;

    /** @var string  */
    public string $transactionCompensation;

    /** @var string|null  */
    public string | null $dateTransactionCompensation;

    /** @var string  */
    public string $dateEntryRegister;

    /** @var string  */
    public string $amount;

    /** @var string|null  */
    public string | null $recipient;

    /** @var integer|null  */
    public int | null $memberId;

    /** @var integer  */
    public int $reviewerId;

    /** @var integer  */
    public int $devolution;

    /** @var integer  */
    public int $deleted;
}
