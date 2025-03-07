<?php

namespace App\Domain\SyncStorage\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

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

    /** @var ?string */
    public ?string $status;

    /** @var ?string */
    public ?string $path;
}
