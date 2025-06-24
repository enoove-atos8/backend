<?php

namespace App\Domain\Financial\Exits\Purchases\DataTransferObjects;

use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardPurchaseRepository;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CardPurchaseData extends DataTransferObject
{
    /** @var int|null */
    public ?int $id;

    /** @var int|null */
    public ?int $cardId;

    /** @var int|null */
    public ?int $groupId;

    /** @var string|null */
    public ?string $status;

    /** @var float|null */
    public ?float $amount;

    /** @var int|null */
    public ?int $installments;

    /** @var float|null */
    public ?float $installmentAmount;

    /** @var string|null */
    public ?string $establishmentName;

    /** @var string|null */
    public ?string $purchaseDescription;

    /** @var string|null */
    public ?string $date;

    /** @var bool|null */
    public ?bool $deleted;

    /** @var string|null */
    public ?string $receipt;




    /**
     * Create a CardPurchaseData instance from an array response.
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
            'groupId' => $data['group_id'] ?? null,
            'status' => $data['status'] ?? null,
            'amount' => isset($data['amount']) ? (float) $data['amount'] : null,
            'installments' => $data['installments'] ?? null,
            'installmentAmount' => $data['installment_amount'] ?? null,
            'establishmentName' => $data['establishment_name'] ?? null,
            'purchaseDescription' => $data['purchase_description'] ?? null,
            'date' => $data['date'] ?? null,
            'deleted' => $data['deleted'] ?? false,
            'receipt' => $data['receipt'] ?? null,
        ]);
    }




    /**
     * Create a CardPurchaseData instance from another CardPurchaseData object.
     *
     * @param CardPurchaseData $purchaseData
     * @param array $additionalData
     * @return self
     * @throws UnknownProperties
     */
    public static function fromSelf(CardPurchaseData $purchaseData, array $additionalData = []): self
    {
        $data = [
            'id' => $purchaseData->id,
            'cardId' => $purchaseData->cardId,
            'groupId' => $purchaseData->groupId,
            'status' => $purchaseData->status,
            'amount' => $purchaseData->amount,
            'installments' => $purchaseData->installments,
            'installmentAmount' => $purchaseData->installmentAmount,
            'establishmentName' => $purchaseData->establishmentName,
            'purchaseDescription' => $purchaseData->purchaseDescription,
            'date' => $purchaseData->date,
            'deleted' => $purchaseData->deleted,
            'receipt' => $purchaseData->receipt,
        ];

        return new self(array_merge($data, $additionalData));
    }


    /**
     * Create a CardPurchaseData instance from another CardPurchaseData object.
     *
     * @param SyncStorageData $syncStorageData
     * @param int $cardId
     * @param array $additionalData
     * @return self
     * @throws UnknownProperties
     */
    public static function fromSyncStorageData(SyncStorageData $syncStorageData, int $cardId, array $additionalData = []): self
    {
        $data = [
            'cardId' => $cardId,
            'groupId' => $syncStorageData->groupId,
            'status' => CardPurchaseRepository::INVOICED_VALUE,
            'amount' => $syncStorageData->purchaseCreditCardAmount,
            'installments' => $syncStorageData->numberInstallments,
            'installmentAmount' => $syncStorageData->purchaseCreditCardInstallmentAmount,
            'establishmentName' => $syncStorageData->establishmentName,
            'purchaseDescription' => $syncStorageData->purchaseDescription,
            'date' => $syncStorageData->purchaseCreditCardDate,
            'deleted' => false,
            'receipt' => $syncStorageData->path ?? null,
        ];

        return new self(array_merge($data, $additionalData));
    }
}
