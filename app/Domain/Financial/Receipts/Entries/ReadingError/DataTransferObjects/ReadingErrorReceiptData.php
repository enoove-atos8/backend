<?php

namespace Domain\Financial\Receipts\Entries\ReadingError\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ReadingErrorReceiptData extends DataTransferObject
{
    /** @var integer | null  */
    public int | null $groupReturnedId;

    /** @var integer | null  */
    public int | null $groupReceivedId;

    /** @var string | null  */
    public string | null $entryType;

    /** @var string | null  */
    public string | null $amount;

    /** @var string | null  */
    public string | null $institution;

    /** @var string | null  */
    public string | null $reason;

    /** @var boolean | null  */
    public bool | null $devolution;

    /** @var integer | null  */
    public int | null $deleted;

    /** @var string | null  */
    public string | null $receiptLink;
}
