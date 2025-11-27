<?php

namespace Domain\CentralDomain\Billing\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class BoletoPaymentData extends DataTransferObject
{
    const BARCODE_KEY = 'barcode';

    const PDF_URL_KEY = 'pdf_url';

    const EXPIRES_AT_KEY = 'expires_at';

    public string $barcode;

    public string $pdfUrl;

    public string $expiresAt;

    /**
     * Create BoletoPaymentData from array response
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            barcode: $data[self::BARCODE_KEY] ?? null,
            pdfUrl: $data[self::PDF_URL_KEY] ?? null,
            expiresAt: $data[self::EXPIRES_AT_KEY] ?? null,
        );
    }
}
