<?php

namespace App\Domain\Financial\Exits\DuplicitiesAnalisys\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DuplicityAnalisysExitsData extends DataTransferObject
{

    /** @var array|null */
    public array|null $exitType;

    /** @var float | null */
    public float | null $amount;

    /** @var string|null */
    public string|null $transactionType;

    /** @var string|null */
    public string|null $dateTransactionCompensation;

    /** @var array|null */
    public array|null $divisionId;

    /** @var string|null */
    public string|null $divisionName;

    /** @var array|null */
    public array|null $groupId;

    /** @var string|null */
    public string|null $groupName;

    /** @var array|null */
    public array|null $paymentCategoryId;

    /** @var array|null */
    public array|null $paymentItemId;

    /** @var integer | null */
    public int | null $repetitionCount;

    /** @var boolean|null */
    public bool | null $duplicityVerified;

    /** @var array | null */
    public array | null $duplicateIds;



    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'exitType' => json_decode(str_replace("'", '"', $data['exit_type'] ?? ''), true) ?? null,
            'amount' => $data['amount'] ?? null,
            'transactionType' => $data['transaction_type'] ?? null,
            'dateTransactionCompensation' => $data['date_transaction_compensation'] ?? null,
            'divisionId' => json_decode(str_replace("'", '"', $data['division_id'] ?? ''), true) ?? null,
            'divisionName' => $data['division_name'] ?? null,
            'groupId' => json_decode(str_replace("'", '"', $data['group_id'] ?? ''), true) ?? null,
            'groupName' => $data['group_name'] ?? null,
            'paymentCategoryId' => json_decode(str_replace("'", '"', $data['payment_category_id'] ?? ''), true) ?? null,
            'paymentItemId' => json_decode(str_replace("'", '"', $data['payment_item_id'] ?? ''), true) ?? null,
            'repetitionCount' => $data['repetition_count'] ?? null,
            'duplicityVerified' => $data['duplicity_verified'] ?? null,
            'duplicateIds' => json_decode(str_replace("'", '"', $data['duplicate_ids']), true) ?? null,
        ]);
    }
}
