<?php

namespace Domain\Ecclesiastical\Divisions\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class DivisionData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var string  */
    public string $slug;

    /** @var string  */
    public string $name;

    /** @var null | string  */
    public null | string $description;

    /** @var boolean  */
    public bool $requireLeader;

    /** @var boolean  */
    public bool $enabled;

}
