<?php

namespace Domain\Churches\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ChurchData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var string  */
    public string $tenantId;

    /** @var string  */
    public string $name;

    /** @var boolean  */
    public bool $activated;

    /** @var string  */
    public string $docType;

    /** @var string  */
    public string $docNumber;

}
