<?php

namespace App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

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



    /**
     * @throws UnknownProperties
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['payment_category_id'] ?? null,
            slug: $data['payment_category_slug'] ?? null,
            name: $data['payment_category_name'] ?? null,
            description: $data['payment_category_description'] ?? null,
        );
    }
}
