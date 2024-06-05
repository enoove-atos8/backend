<?php

namespace App\Domain\Financial\Entries\General\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class EntryData extends DataTransferObject
{
    /** @var string  */
    public string $entryType;

    /** @var string|null  */
    public string | null $transactionType;

    /** @var string|null  */
    public string | null $transactionCompensation;

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
    public int $residualValue;

    /** @var integer  */
    public int $deleted;

    /** @var string|null  */
    public string | null $comments;

    /** @var string|null  */
    public string | null $receipt;

}
