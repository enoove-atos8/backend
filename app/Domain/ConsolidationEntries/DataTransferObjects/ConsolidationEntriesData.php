<?php

namespace Domain\ConsolidationEntries\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ConsolidationEntriesData extends DataTransferObject
{
    /** @var integer */
    public int $id = 0;

    /** @var string|null */
    public string|null $date;

    /** @var boolean|null */
    public bool|null $consolidated = false;
}
