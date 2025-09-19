<?php

namespace App\Domain\Financial\Entries\Entries\DataTransferObjects;

use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use DateTime;
use Exception;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class EntryData extends DataTransferObject
{
    /** @var integer | null  */
    public ?int $id;

    /** @var integer | null  */
    public ?int $memberId;

    /** @var integer | null  */
    public ?int $accountId;

    /** @var integer | null  */
    public ?int $cultId;

    /** @var integer | null  */
    public ?int $reviewerId;

    /** @var integer | null  */
    public ?int $groupReturnedId;

    /** @var integer | null  */
    public ?int $groupReceivedId;

    /** @var integer|null  */
    public ?int $identificationPending;

    /** @var string | null  */
    public ?string $entryType;

    /** @var string | null  */
    public ?string $transactionType;

    /** @var string | null  */
    public ?string $transactionCompensation;

    /** @var string | null  */
    public ?string $dateTransactionCompensation;

    /** @var string | null  */
    public ?string $dateEntryRegister;

    /** @var string | null  */
    public ?string $amount;

    /** @var string | null  */
    public ?string $recipient;

    /** @var boolean | null  */
    public ?bool $duplicityVerified;

    /** @var string | null  */
    public ?string $timestampValueCpf;

    /** @var integer | null  */
    public ?int $devolution;

    /** @var integer | null  */
    public ?int $residualValue;

    /** @var integer | null  */
    public ?int $deleted;

    /** @var string | null  */
    public ?string $comments;

    /** @var string | null  */
    public ?string $receipt;

    /**
     * Create an EntryData instance from extracted data
     *
     * @param array $extractedData Data extracted from receipt
     * @param mixed $member Member entity or null
     * @param SyncStorageData $data Sync storage data
     * @param object $reviewer Reviewer object
     * @param string|null $returnReceivingGroupId Return receiving group ID (if applicable)
     * @param string|null $nextBusinessDay Function to get next business day from a date
     * @return self New EntryData instance
     * @throws UnknownProperties
     * @throws Exception
     */
    public static function fromExtractedData(
        array $extractedData,
        mixed $member,
        SyncStorageData $data,
        object $reviewer,
        ?string $returnReceivingGroupId = null,
        ?string $nextBusinessDay = null
    ): self {
        $currentDate = date('Y-m-d');
        $extractedDate = $extractedData['data']['date'];

        $dateTransactionCompensation = $nextBusinessDay ?
            $nextBusinessDay . 'T03:00:00.000Z' :
            (new DateTime($extractedDate))->format('Y-m-d') . 'T03:00:00.000Z';

        $instance = new self([
            'id' => null,
            'amount' => floatval($extractedData['data']['amount']) / 100,
            'comments' => 'Entrada registrada automaticamente!',
            'dateEntryRegister' => $currentDate,
            'dateTransactionCompensation' => $dateTransactionCompensation,
            'deleted' => 0,
            'entryType' => $data->docSubType,
            'memberId' => $member?->id,
            'accountId' => $data?->accountId,
            'receipt' => null,
            'duplicity_verified' => false,
            'devolution' => 0,
            'residualValue' => 0,
            'identificationPending' => 0,
            'cultId' => null,
            'timestampValueCpf' => null,
            'reviewerId' => $reviewer->id,
            'transactionCompensation' => EntryRepository::COMPENSATED_VALUE,
            'transactionType' => EntryRepository::PIX_TRANSACTION_TYPE,
            'groupReceivedId' => null,
            'groupReturnedId' => null,
        ]);

        if ($data->docSubType == EntryRepository::DESIGNATED_VALUE) {
            $instance->groupReceivedId = $data->groupId;

            if ($data->isDevolution == 1) {
                $instance->devolution = 1;
                $instance->groupReceivedId = $returnReceivingGroupId;
                $instance->groupReturnedId = $data->groupId;
            }
        }

        if ($data->docSubType == EntryRepository::TITHE_VALUE) {
            $instance->groupReceivedId = null;
            $instance->devolution = 0;
            $instance->groupReturnedId = null;
        }

        return $instance;
    }
}
