<?php

namespace Domain\CentralDomain\Plans\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PlanData extends DataTransferObject
{
    /** @var integer */
    public int $id = 0;

    /** @var string|null */
    public string|null $name;

    /** @var string|null */
    public string|null $description;

    /** @var string|null */
    public string|null $price;

    /** @var boolean|null */
    public bool|null $activated;
}
