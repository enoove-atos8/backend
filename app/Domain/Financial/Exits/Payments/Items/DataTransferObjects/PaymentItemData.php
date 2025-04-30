<?php

namespace App\Domain\Financial\Exits\Payments\Items\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

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

    /** @var string | null */
    public ?string $description;

    /** @var bool | null */
    public ?bool $deleted = false;


    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['payment_item_id'] ?? null,
            paymentCategoryId: $data['payment_item_payment_category_id'] ?? null,
            slug: $data['payment_item_slug'] ?? null,
            name: $data['payment_item_name'] ?? null,
            description: $data['payment_item_description'] ?? null,
            deleted: $data['payment_item_deleted'] ?? null,
        );
    }
}
