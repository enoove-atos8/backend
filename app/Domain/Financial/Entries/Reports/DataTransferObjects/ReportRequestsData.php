<?php

namespace App\Domain\Financial\Entries\Reports\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ReportRequestsData extends DataTransferObject
{
    /** @var integer | null  */
    public int | null $id;

    /** @var string | null  */
    public string | null $reportName;

    /** @var boolean | null  */
    public bool | null $detailedReport;

    /** @var string | null  */
    public string | null $generationDate;

    /** @var array | null  */
    public array | null $dates;

    /** @var string|null  */
    public string | null $status;

    /** @var string|null  */
    public string | null $error;

    /** @var integer | null  */
    public int | null $startedBy;

    /** @var array | null  */
    public array | null $entryTypes;

    /** @var integer | null  */
    public int | null $groupReceivedId;

    /** @var boolean | null  */
    public bool | null $dateOrder;

    /** @var boolean | null  */
    public bool | null $allGroupsReceipts;

}
