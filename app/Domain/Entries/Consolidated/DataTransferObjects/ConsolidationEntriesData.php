<?php

namespace Domain\Entries\Consolidated\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ConsolidationEntriesData extends DataTransferObject
{
    /** @var integer */
    public int $id = 0;

    /** @var string|null */
    public string|null $date;

    /** @var boolean|null */
    public bool|null $consolidated = false;

    /** @var string|null */
    public string|null $designatedAmount;

    /** @var string|null */
    public string|null $offersAmount;

    /** @var string|null */
    public string|null $titheAmount;

    /** @var string|null */
    public string|null $totalAmount;
}
