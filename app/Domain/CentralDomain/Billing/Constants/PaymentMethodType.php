<?php

namespace App\Domain\CentralDomain\Billing\Constants;

class PaymentMethodType
{
    const CARD = 'card';

    const BOLETO = 'boleto';

    const PIX = 'pix';

    const ALLOWED_TYPES = [
        self::BOLETO,
        self::PIX,
    ];

    /**
     * Check if payment method type is valid for invoice payment
     */
    public static function isValidForInvoicePayment(string $type): bool
    {
        return in_array($type, self::ALLOWED_TYPES, true);
    }
}
