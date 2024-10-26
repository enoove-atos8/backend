<?php

namespace App\Domain\Financial\Entries\General\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class EntryData extends DataTransferObject
{
    /** @var integer | null  */
    public int | null $memberId;

    /** @var integer | null  */
    public int | null $reviewerId;

    /** @var integer | null  */
    public int | null $cultFinancialDataId;

    /** @var integer | null  */
    public int | null $groupReturnedId;

    /** @var integer | null  */
    public int | null $groupReceivedId;

    /** @var integer|null  */
    public int | null $identificationPending;

    /** @var string | null  */
    public string | null $entryType;

    /** @var string | null  */
    public string | null $transactionType;

    /** @var string | null  */
    public string | null $transactionCompensation;

    /** @var string | null  */
    public string | null $dateTransactionCompensation;

    /** @var string | null  */
    public string | null $dateEntryRegister;

    /** @var string | null  */
    public string | null $amount;

    /** @var string | null  */
    public string | null $recipient;

    /** @var string | null  */
    public string | null $timestampValueCpf;

    /** @var integer | null  */
    public int | null $devolution;

    /** @var integer | null  */
    public int | null $residualValue;

    /** @var integer | null  */
    public int | null $deleted;

    /** @var string | null  */
    public string | null $comments;

    /** @var string | null  */
    public string | null $receipt;

}
