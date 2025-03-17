<?php

namespace Domain\Financial\Exits\PaymentItem\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentItemData extends DataTransferObject
{
    /** @var integer | null */
    public ?int $id;

    /** @var integer | null */
    public ?int $paymentCategoryId;

    /** @var string | null */
    public ?string $slug;

    /** @var string | null */
    public ?string $name;
}
