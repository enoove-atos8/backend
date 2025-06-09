<?php

namespace App\Domain\Financial\Exits\Purchases\DataTransferObjects;

use Illuminate\Support\Carbon;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CardInstallmentData extends DataTransferObject
{
    /** @var int|null */
    public ?int $id;

    /** @var int|null */
    public ?int $cardId;

    /** @var CardPurchaseData|null */
    public ?CardPurchaseData $cardPurchaseData;

    /** @var CardInvoiceData|null */
    public ?CardInvoiceData $cardInvoiceData;

    /** @var string|null */
    public ?string $status;

    /** @var int|null */
    public ?int $installment;

    /** @var int|null */
    public ?int $installmentAmount;

    /** @var string|null */
    public ?string $date;

    /** @var bool|null */
    public ?bool $deleted;

    /** @var bool|null */
    public ?bool $isFirstInstallment;



    /**
     * Create a CardInstallmentData instance from an array response.
     *
     * @param array $data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['cards_installments_id'] ?? null,
            cardInvoiceData: new CardInvoiceData([
                'id' => $data['cards_invoices_id'] ?? null,
                'status' => $data['cards_invoices_status'] ?? null,
                'amount' => isset($data['cards_invoices_amount']) ? (float) $data['cards_invoices_amount'] : null,
            ]),
            cardPurchaseData: new CardPurchaseData([
                'id' => $data['cards_purchases_id'] ?? null,
                'cardId' => $data['cards_purchases_card_id'] ?? null,
                'status' => $data['cards_purchases_status'] ?? null,
                'amount' => isset($data['cards_purchases_amount']) ? (float) $data['cards_purchases_amount'] : null,
                'installments' => $data['cards_purchases_installments'] ?? null,
                'installmentAmount' => $data['cards_purchases_installment_amount'] ?? null,
                'date' => $data['cards_purchases_date'] ?? null,
                'deleted' => $data['cards_purchases_deleted'] ?? false,
                'receipt' => $data['cards_purchases_receipt'] ?? null,
            ]),
            status: $data['cards_installments_status'] ?? null,
            installment: $data['cards_installments_installment'] ?? null,
            installmentAmount: $data['cards_installments_installment_amount'] ?? null,
            date: $data['cards_installments_date'] ?? null,
            deleted: $data['cards_installments_deleted'] ?? false,
        );
    }

    /**
     * Create a CardInstallmentData instance from another CardInstallmentData object.
     *
     * @param CardInstallmentData $installmentData
     * @param CardInvoiceData $cardInvoiceData
     * @param CardPurchaseData $cardPurchaseData
     * @param array $additionalData
     * @return self
     * @throws UnknownProperties
     */
    public static function fromSelf(
        CardInstallmentData $installmentData,
        CardInvoiceData $cardInvoiceData,
        CardPurchaseData $cardPurchaseData,
        array $additionalData = []): self
    {
        $data = [
            'id' => $installmentData->id,
            'cardId' => $installmentData->cardId,
            'invoice' => new CardInvoiceData(['id' => $cardInvoiceData->id]),
            'purchase' => new CardPurchaseData(['id' => $cardPurchaseData->id]),
            'status' => $installmentData->status,
            'installment' => $installmentData->installment,
            'installmentAmount' => $installmentData->installmentAmount,
            'date' => $installmentData->date,
            'deleted' => $installmentData->deleted,
            'isFirstInstallment' => $installmentData->isFirstInstallment,
        ];

        return new self(array_merge($data, $additionalData));
    }

    /**
     * Create a CardInstallmentData instance from SyncStorageData.
     *
     * @param CardPurchaseData $cardPurchaseData
     * @param int $purchaseId
     * @param int $currentInstallment
     * @param bool $isFirstInstallment
     * @param array $additionalData
     * @return self
     * @throws UnknownProperties
     */
    public static function fromPurchaseData(CardPurchaseData $cardPurchaseData, int $purchaseId, int $currentInstallment, bool $isFirstInstallment, array $additionalData = []): self
    {
        $purchaseDate = Carbon::parse($cardPurchaseData->date);

        $referenceDate = $purchaseDate->copy()->addMonthsNoOverflow($currentInstallment - 1);
        $date = $referenceDate->format('Y-m-d');

        $data = [
            'cardId' => $cardPurchaseData->cardId,
            'invoiceId' => null,
            'purchaseId' => $purchaseId,
            'status' => $cardPurchaseData->status,
            'amount' => $cardPurchaseData->amount,
            'installment' => $currentInstallment,
            'installmentAmount'  => $cardPurchaseData->installmentAmount,
            'date' => $date,
            'deleted' => false,
            'isFirstInstallment' => $isFirstInstallment,
        ];

        return new self(array_merge($data, $additionalData));
    }
}
