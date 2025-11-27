<?php

namespace Domain\CentralDomain\Billing\DataTransferObjects;

use App\Domain\CentralDomain\Billing\Constants\PaymentMethodType;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class InvoiceData extends DataTransferObject
{
    const ID_KEY = 'id';

    const NUMBER_KEY = 'number';

    const AMOUNT_KEY = 'amount';

    const STATUS_KEY = 'status';

    const DUE_DATE_KEY = 'due_date';

    const PAID_AT_KEY = 'paid_at';

    const PAYMENT_METHOD_KEY = 'payment_method';

    const PAYMENT_METHOD_DETAILS_KEY = 'payment_method_details';

    const INVOICE_PDF_KEY = 'invoice_pdf';

    const HOSTED_INVOICE_URL_KEY = 'hosted_invoice_url';

    const CARD_DETAILS_SEPARATOR = ' •••• ';

    public string $id;

    public ?string $number;

    public int $amount;

    public string $status;

    public ?string $dueDate;

    public ?string $paidAt;

    public ?string $paymentMethod;

    public ?string $paymentMethodDetails;

    public ?string $invoicePdf;

    public ?string $hostedInvoiceUrl;

    /**
     * Create InvoiceData from array response
     *
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data[self::ID_KEY] ?? null,
            number: $data[self::NUMBER_KEY] ?? null,
            amount: $data[self::AMOUNT_KEY] ?? 0,
            status: $data[self::STATUS_KEY] ?? null,
            dueDate: $data[self::DUE_DATE_KEY] ?? null,
            paidAt: $data[self::PAID_AT_KEY] ?? null,
            paymentMethod: $data[self::PAYMENT_METHOD_KEY] ?? null,
            paymentMethodDetails: $data[self::PAYMENT_METHOD_DETAILS_KEY] ?? null,
            invoicePdf: $data[self::INVOICE_PDF_KEY] ?? null,
            hostedInvoiceUrl: $data[self::HOSTED_INVOICE_URL_KEY] ?? null,
        );
    }

    /**
     * Format payment method details for card
     */
    public static function formatCardDetails(string $brand, string $last4): string
    {
        return ucfirst($brand) . self::CARD_DETAILS_SEPARATOR . $last4;
    }

    /**
     * Format payment method details for boleto
     */
    public static function formatBoletoDetails(): string
    {
        return ucfirst(PaymentMethodType::BOLETO);
    }

    /**
     * Format payment method details for PIX
     */
    public static function formatPixDetails(): string
    {
        return strtoupper(PaymentMethodType::PIX);
    }
}
