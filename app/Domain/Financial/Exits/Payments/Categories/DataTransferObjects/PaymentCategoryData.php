<?php

namespace App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentCategoryData extends DataTransferObject
{
    /** @var integer | null */
    public ?int $id;

    /** @var string | null */
    public ?string $slug;

    /** @var string | null */
    public ?string $name;

    /** @var string | null */
    public ?string $description;
}
