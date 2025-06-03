<?php

namespace App\Domain\Financial\Exits\Purchases\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CardInvoiceData extends DataTransferObject
{
    /** @var int|null */
    public ?int $id;

    /** @var int|null */
    public ?int $cardId;

    /** @var string|null */
    public ?string $status;

    /** @var float|null */
    public ?float $amount;

    /** @var string|null */
    public ?string $referenceDate;

    /** @var string|null */
    public ?string $paymentDate;

    /** @var string|null */
    public ?string $paymentMethod;

    /** @var bool|null */
    public ?bool $isClosed;

    /** @var bool|null */
    public ?bool $deleted;



    /**
     * Create a CardInvoiceData instance from an array response.
     *
     * @param array $data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'cardId' => $data['card_id'] ?? null,
            'status' => $data['status'] ?? null,
            'amount' => isset($data['amount']) ? (float) $data['amount'] : null,
            'referenceDate' => $data['reference_date'] ?? null,
            'paymentDate' => $data['payment_date'] ?? null,
            'paymentMethod' => $data['payment_method'] ?? null,
            'isClosed' => $data['is_closed'] ?? null,
            'deleted' => $data['deleted'] ?? null,
        ]);
    }



    /**
     * Create a CardInvoiceData instance from another CardInvoiceData object
     *
     * @param CardInvoiceData $invoiceData
     * @param array $additionalData
     * @return self
     * @throws UnknownProperties
     */
    public static function fromSelf(CardInvoiceData $invoiceData, array $additionalData = []): self
    {
        $data = [
            'id' => $invoiceData->id,
            'cardId' => $invoiceData->cardId,
            'status' => $invoiceData->status,
            'amount' => $invoiceData->amount,
            'referenceDate' => $invoiceData->referenceDate,
            'paymentDate' => $invoiceData->paymentDate,
            'paymentMethod' => $invoiceData->paymentMethod,
            'isClosed' => $invoiceData->isClosed,
            'deleted' => $invoiceData->deleted,
        ];

        return new self(array_merge($data, $additionalData));
    }


    /**
     * Create a CardInvoiceData instance
     *
     * @param CardInstallmentData $cardInstallmentData
     * @param array $additionalData
     * @return self
     * @throws UnknownProperties
     */
    public static function fromInstallmentData(CardInstallmentData $cardInstallmentData, array $additionalData = []): self
    {
        $data = [
            'cardId'        => $cardInstallmentData->cardId,
            'status'        => 'open',
            'amount'        => null,
            'referenceDate' => null,
            'paymentDate'   => null,
            'paymentMethod' => null,
            'isClosed'      => false,
            'deleted'       => false,
        ];

        return new self(array_merge($data, $additionalData));
    }
}
