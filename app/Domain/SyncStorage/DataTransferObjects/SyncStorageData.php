<?php

namespace App\Domain\SyncStorage\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class SyncStorageData extends DataTransferObject
{
    /** @var ?integer */
    public ?int $id;

    /** @var ?string */
    public ?string $tenant;

    /** @var ?string */
    public ?string $module;

    /** @var ?string */
    public ?string $docType;

    /** @var ?string */
    public ?string $docSubType;

    /** @var ?string */
    public ?string $divisionId;

    /** @var ?string | null */
    public ?string $groupId;

    /** @var ?string | null */
    public ?string $paymentCategoryId;

    /** @var ?string | null */
    public ?string $paymentItemId;

    /** @var ?string | null */
    public ?string $cardId;

    /** @var ?string | null */
    public ?string $invoiceClosedDay;

    /** @var ?bool */
    public ?bool $isPayment;

    /** @var ?bool */
    public ?bool $isDevolution;

    /** @var ?bool */
    public ?bool $isCreditCardPurchase;

    /** @var ?string | null */
    public ?string $creditCardDueDate;

    /** @var ?string | null */
    public ?string $numberInstallments;

    /** @var ?string | null */
    public ?string $purchaseCreditCardDate;

    /** @var ?float | null */
    public ?float $purchaseCreditCardAmount;

    /** @var ?float | null */
    public ?float $purchaseCreditCardInstallmentAmount;

    /** @var ?string */
    public ?string $status;

    /** @var ?string */
    public ?string $path;



    /**
     * Create a SyncStorageData instance from an array response.
     *
     * @param array $data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'tenant' => $data['tenant'] ?? null,
            'module' => $data['module'] ?? null,
            'docType' => $data['doc_type'] ?? null,
            'docSubType' => $data['doc_sub_type'] ?? null,
            'divisionId' => $data['division_id'] ?? null,
            'groupId' => $data['group_id'] ?? null,
            'paymentCategoryId' => $data['payment_category_id'] ?? false,
            'paymentItemId' => $data['payment_item_id'] ?? null,
            'cardId' => $data['card_id'] ?? null,
            'invoiceClosedDay' => $data['invoice_closed_day'] ?? null,
            'isPayment' => $data['is_payment'] ?? null,
            'isDevolution' => $data['is_devolution'] ?? null,
            'isCreditCardPurchase' => $data['is_credit_card_purchase'] ?? null,
            'creditCardDueDate' => $data['credit_card_due_date'] ?? null,
            'numberInstallments' => $data['number_installments'] ?? null,
            'purchaseCreditCardDate' => $data['purchase_credit_card_date'] ?? null,
            'purchaseCreditCardAmount' => $data['purchase_credit_card_amount'] ?? null,
            'purchaseCreditCardInstallmentAmount' => $data['purchase_credit_card_installment_amount'] ?? null,
            'status' => $data['status'] ?? null,
            'path' => $data['path'] ?? null,
        ]);
    }
}
