<?php

namespace App\Domain\CentralDomain\Billing\Constants;

class InvoiceStatus
{
    // Application Status
    const PAID = 'paid';

    const PENDING = 'pending';

    const FAILED = 'failed';

    const VOID = 'void';

    // Stripe Status
    const STRIPE_PAID = 'paid';

    const STRIPE_OPEN = 'open';

    const STRIPE_DRAFT = 'draft';

    const STRIPE_UNCOLLECTIBLE = 'uncollectible';

    const STRIPE_VOID = 'void';

    /**
     * Map Stripe status to application status
     */
    public static function fromStripeStatus(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            self::STRIPE_PAID => self::PAID,
            self::STRIPE_OPEN => self::PENDING,
            self::STRIPE_DRAFT => self::PENDING,
            self::STRIPE_UNCOLLECTIBLE => self::FAILED,
            self::STRIPE_VOID => self::VOID,
            default => self::PENDING,
        };
    }
}
