<?php

namespace Domain\Ecclesiastical\Groups\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class GroupData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var string  */
    public string $ecclesiasticalDivisionId;

    /** @var integer  */
    public int $parentGroupId;

    /** @var string  */
    public string $name;

    /** @var string  */
    public string $description;

    /** @var boolean  */
    public bool $financialTransactionExist;

    /** @var boolean  */
    public bool $enabled;

    /** @var boolean  */
    public bool $temporaryEvent;

    /** @var string  */
    public string $startDate;

    /** @var string  */
    public string $endDate;

}
