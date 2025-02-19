<?php

namespace Domain\Mobile\SyncStorage\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class SyncStorageData extends DataTransferObject
{
    /** @var ?string */
    public ?string $tenant;

    /** @var ?string */
    public ?string $module;

    /** @var ?string */
    public ?string $docType;

    /** @var ?string */
    public ?string $docSubType;

    /** @var ?int | null */
    public ?int $divisionId;

    /** @var ?int | null */
    public ?int $groupId;

    /** @var ?int | null */
    public ?int $paymentCategoryId;

    /** @var ?int | null */
    public ?int $paymentItemId;

    /** @var ?bool */
    public ?bool $isPayment;

    /** @var ?bool */
    public ?bool $isCreditCardPurchase;

    /** @var ?string | null */
    public ?string $creditCardDueDate;

    /** @var ?int | null */
    public ?int $numberInstallments;

    /** @var ?string | null */
    public ?string $purchaseCreditCardDate;

    /** @var ?float | null */
    public ?float $purchaseCreditCardAmount;

    /** @var ?string */
    public ?string $status;

    /** @var ?string */
    public ?string $path;
}
