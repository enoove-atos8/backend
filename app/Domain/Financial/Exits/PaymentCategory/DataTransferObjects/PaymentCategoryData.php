<?php

namespace Domain\Financial\Exits\PaymentCategory\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentCategoryData extends DataTransferObject
{
    /** @var integer | null */
    public ?int $id;

    /** @var string | null */
    public ?string $slug;

    /** @var string | null */
    public ?string $name;
}
