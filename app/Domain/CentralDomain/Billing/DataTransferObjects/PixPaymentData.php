<?php

namespace Domain\CentralDomain\Billing\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PixPaymentData extends DataTransferObject
{
    const QR_CODE_KEY = 'qr_code';

    const QR_CODE_URL_KEY = 'qr_code_url';

    const EXPIRES_AT_KEY = 'expires_at';

    public string $qrCode;

    public string $qrCodeUrl;

    public string $expiresAt;

    /**
     * Create PixPaymentData from array response
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            qrCode: $data[self::QR_CODE_KEY] ?? null,
            qrCodeUrl: $data[self::QR_CODE_URL_KEY] ?? null,
            expiresAt: $data[self::EXPIRES_AT_KEY] ?? null,
        );
    }
}
