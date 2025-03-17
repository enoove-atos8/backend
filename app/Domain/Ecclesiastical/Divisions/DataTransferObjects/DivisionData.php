<?php

namespace Domain\Ecclesiastical\Divisions\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class DivisionData extends DataTransferObject
{
    /** @var null | integer  */
    public ?int $id = 0;

    /** @var null | string  */
    public ?string $slug;

    /** @var null | string  */
    public ?string $name;

    /** @var null | string  */
    public ?string $description;

    /** @var null | boolean  */
    public ?bool $requireLeader;

    /** @var null | boolean  */
    public ?bool $enabled;

}
