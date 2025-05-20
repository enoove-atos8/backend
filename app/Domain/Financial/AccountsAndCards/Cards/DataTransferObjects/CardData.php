<?php

namespace Domain\Financial\AccountsAndCards\Cards\DataTransferObjects;

use Domain\Financial\AccountsAndCards\Cards\Models\Card;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CardData extends DataTransferObject
{
    /** @var int|null */
    public ?int $id;

    /** @var string|null */
    public ?string $name;

    /** @var string|null */
    public ?string $description;

    /** @var string|null */
    public ?string $cardNumber;

    /** @var string|null */
    public ?string $expiryDate;

    /** @var string|null */
    public ?string $dueDay;

    /** @var string|null */
    public ?string $closingDay;


    /** @var bool|null */
    public ?bool $status;

    /** @var bool|null */
    public ?bool $active;

    /** @var bool|null */
    public ?bool $deleted;

    /** @var string|null */
    public ?string $creditCardBrand;

    /** @var string|null */
    public ?string $personType;

    /** @var string|null */
    public ?string $cardHolderName;

    /** @var float|null */
    public ?float $limit;

    /**
     * Create a CardData instance from an array response.
     *
     * @param array $data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? null,
            'cardNumber' => $data['card_number'] ?? '',
            'expiryDate' => $data['expiry_date'] ?? null,
            'dueDay' => $data['due_day'] ?? null,
            'closingDay' => $data['closing_day'] ?? null,
            'status' => $data['status'] ?? true,
            'active' => $data['active'] ?? true,
            'deleted' => $data['deleted'],
            'creditCardBrand' => $data['credit_card_brand'] ?? null,
            'personType' => $data['person_type'] ?? null,
            'cardHolderName' => $data['card_holder_name'] ?? null,
            'limit' => $data['limit'] ?? null,
        ]);
    }


    /**
     * Create a CardData instance from another CardData object
     *
     * @param CardData $cardData Existing CardData object
     * @param array $additionalData Optional additional data to override or add
     * @return self
     * @throws UnknownProperties
     */
    public static function fromSelf(CardData $cardData, array $additionalData = []): self
    {
        $data = [
            'id' => $cardData->id,
            'name' => $cardData->name,
            'description' => $cardData->description,
            'cardNumber' => $cardData->cardNumber,
            'expiryDate' => $cardData->expiryDate,
            'dueDay' => $cardData->dueDay,
            'closingDay' => $cardData->closingDay,
            'status' => $cardData->status,
            'active' => $cardData->active,
            'deleted' => $cardData->deleted,
            'creditCardBrand' => $cardData->creditCardBrand,
            'personType' => $cardData->personType,
            'cardHolderName' => $cardData->cardHolderName,
            'limit' => $cardData->limit,
        ];

        return new self(array_merge($data, $additionalData));
    }
}
