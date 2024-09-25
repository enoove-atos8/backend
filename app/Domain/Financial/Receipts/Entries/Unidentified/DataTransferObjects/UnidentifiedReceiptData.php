<?php

namespace Domain\Financial\Receipts\Entries\Unidentified\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class UnidentifiedReceiptData extends DataTransferObject
{
    /** @var string | null  */
    public string | null $entryType;

    /** @var string | null  */
    public string | null $amount;

    /** @var integer | null  */
    public int | null $deleted;

    /** @var string | null  */
    public string | null $receiptLink;
}
