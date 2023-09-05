<?php

namespace Domain\Entries\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class EntryData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var string  */
    public string $entryType;

    /** @var string  */
    public string $transactionType;

    /** @var string  */
    public string $transactionCompensation;

    /** @var string  */
    public string $dateTransactionCompensation;

    /** @var string  */
    public string $dateEntryRegister;

    /** @var string  */
    public string $amount;

    /** @var string  */
    public string $recipient;

    /** @var integer  */
    public int $memberId;

    /** @var integer  */
    public int $reviewerId;
}
