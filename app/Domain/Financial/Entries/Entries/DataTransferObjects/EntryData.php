<?php

namespace App\Domain\Financial\Entries\Entries\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class EntryData extends DataTransferObject
{
    /** @var integer | null  */
    public ?int $id;

    /** @var integer | null  */
    public ?int $memberId;

    /** @var integer | null  */
    public ?int $cultId;

    /** @var integer | null  */
    public ?int $reviewerId;

    /** @var integer | null  */
    public ?int $groupReturnedId;

    /** @var integer | null  */
    public ?int $groupReceivedId;

    /** @var integer|null  */
    public ?int $identificationPending;

    /** @var string | null  */
    public ?string $entryType;

    /** @var string | null  */
    public ?string $transactionType;

    /** @var string | null  */
    public ?string $transactionCompensation;

    /** @var string | null  */
    public ?string $dateTransactionCompensation;

    /** @var string | null  */
    public ?string $dateEntryRegister;

    /** @var string | null  */
    public ?string $amount;

    /** @var string | null  */
    public ?string $recipient;

    /** @var string | null  */
    public ?string $timestampValueCpf;

    /** @var integer | null  */
    public ?int $devolution;

    /** @var integer | null  */
    public ?int $residualValue;

    /** @var integer | null  */
    public ?int $deleted;

    /** @var string | null  */
    public ?string $comments;

    /** @var string | null  */
    public ?string $receipt;

}
