<?php

namespace Domain\Ecclesiastical\Divisions\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class DivisionData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var string  */
    public string $routeResource;

    /** @var string  */
    public string $name;

    /** @var string  */
    public string $description;

    /** @var boolean  */
    public bool $enabled;

}
