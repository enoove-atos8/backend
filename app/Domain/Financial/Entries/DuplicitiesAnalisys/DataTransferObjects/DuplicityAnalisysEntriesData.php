<?php

namespace App\Domain\Financial\Entries\DuplicitiesAnalisys\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DuplicityAnalisysEntriesData extends DataTransferObject
{

    /** @var string|null */
    public string|null $entryType;

    /** @var float | null */
    public float | null $amount;

    /** @var string|null */
    public string|null $transactionType;

    /** @var string|null */
    public string|null $dateTransactionCompensation;

    /** @var integer|null */
    public int | null $memberId;

    /** @var string|null */
    public string|null $memberFullName;

    /** @var integer | null */
    public int | null $repetitionCount;

    /** @var integer | null */
    public int | null $groupReturnedId;


    /** @var integer | null */
    public int | null $groupReceivedId;

    /** @var string | null */
    public string | null $groupReceivedName;

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
            'entryType' => $data['entry_type'] ?? null,
            'amount' => $data['amount'] ?? null,
            'transactionType' => $data['transaction_type'] ?? null,
            'dateTransactionCompensation' => $data['date_transaction_compensation'] ?? null,
            'memberId' => $data['member_id'] ?? null,
            'memberFullName' => $data['member_full_name'] ?? null,
            'repetitionCount' => $data['repetition_count'] ?? null,
            'groupReturnedId' => $data['group_returned_id'] ?? null,
            'groupReceivedId' => $data['group_received_id'] ?? null,
            'groupReceivedName' => $data['group_received_name'] ?? null,
            'duplicityVerified' => $data['duplicity_verified'] ?? null,
            'duplicateIds' => json_decode(str_replace("'", '"', $data['duplicate_ids']), true) ?? null,
        ]);
    }
}
