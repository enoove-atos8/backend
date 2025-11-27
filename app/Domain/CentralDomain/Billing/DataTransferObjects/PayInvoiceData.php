<?php

namespace Domain\CentralDomain\Billing\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PayInvoiceData extends DataTransferObject
{
    const PAYMENT_METHOD_KEY = 'payment_method';

    const TAX_ID_KEY = 'tax_id';

    const NAME_KEY = 'name';

    const EMAIL_KEY = 'email';

    const ADDRESS_LINE1_KEY = 'address_line1';

    const ADDRESS_CITY_KEY = 'address_city';

    const ADDRESS_STATE_KEY = 'address_state';

    const ADDRESS_POSTAL_CODE_KEY = 'address_postal_code';

    public string $paymentMethod;

    public ?string $taxId;

    public ?string $name;

    public ?string $email;

    public ?string $addressLine1;

    public ?string $addressCity;

    public ?string $addressState;

    public ?string $addressPostalCode;

    /**
     * Create PayInvoiceData from array response
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            paymentMethod: $data[self::PAYMENT_METHOD_KEY] ?? null,
            taxId: $data[self::TAX_ID_KEY] ?? null,
            name: $data[self::NAME_KEY] ?? null,
            email: $data[self::EMAIL_KEY] ?? null,
            addressLine1: $data[self::ADDRESS_LINE1_KEY] ?? null,
            addressCity: $data[self::ADDRESS_CITY_KEY] ?? null,
            addressState: $data[self::ADDRESS_STATE_KEY] ?? null,
            addressPostalCode: $data[self::ADDRESS_POSTAL_CODE_KEY] ?? null,
        );
    }
}
