<?php

namespace Application\Api\v1\Billing\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            self::ID_KEY => $this->id,
            self::NUMBER_KEY => $this->number,
            self::AMOUNT_KEY => $this->amount,
            self::STATUS_KEY => $this->status,
            self::DUE_DATE_KEY => $this->dueDate,
            self::PAID_AT_KEY => $this->paidAt,
            self::PAYMENT_METHOD_KEY => $this->paymentMethod,
            self::PAYMENT_METHOD_DETAILS_KEY => $this->paymentMethodDetails,
            self::INVOICE_PDF_KEY => $this->invoicePdf,
            self::HOSTED_INVOICE_URL_KEY => $this->hostedInvoiceUrl,
        ];
    }
}
