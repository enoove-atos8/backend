<?php

namespace App\Domain\Financial\Entries\Cults\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class CultData extends DataTransferObject
{
    /** @var integer */
    public int $id = 0;

    /** @var bool|null */
    public bool|null $worshipWithoutEntries;

 /** @var string|null */
    public string|null $cultDay;

    /** @var string|null */
    public string|null $cultDate;

    /** @var string|null */
    public string|null $dateTransactionCompensation;

    /** @var string|null */
    public string|null $transactionType;

    /** @var string|null */
    public string|null $transactionCompensation;

    /** @var array|null */
    public array|null $tithes;

    /** @var string|null */
    public string|null $amountTithes;

    /** @var array|null */
    public array|null $designated;

    /** @var string|null */
    public string|null $amountDesignated;

    /** @var array|null */
    public array|null $offers;

    /** @var string|null */
    public string|null $amountOffers;

    /** @var integer|null */
    public int|null $deleted;

    /** @var integer|null */
    public int|null $reviewerId;

    /** @var string|null */
    public string|null $receipt;
}
