<?php

namespace Domain\Financial\Movements\DataTransferObjects;

use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MovementsData extends DataTransferObject
{
    /** @var int|null */
    public ?int $id;

    /** @var int|null */
    public ?int $groupId;

    /** @var string|null */
    public ?string $type;

    /** @var string|null */
    public ?string $subType;

    /** @var float|null */
    public ?float $amount;

    /** @var float|null */
    public ?float $balance;

    /** @var string|null */
    public ?string $description;

    /** @var string|null */
    public ?string $movementDate;

    /** @var integer|null */
    public ?int $referenceId;

    /** @var bool|null */
    public ?bool $isInitialBalance;

    /**
     * @throws UnknownProperties
     */
    public static function fromArray(array $data): self
    {
        return new self([
            'groupId' => $data['group_id'] ?? 0,
            'type' => $data['type'] ?? '',
            'subType' => $data['sub_type'] ?? '',
            'amount' => $data['amount'] ?? 0.0,
            'balance' => $data['balance'] ?? 0.0,
            'description' => $data['description'] ?? null,
            'movementDate' => $data['movement_date'] ?? '',
            'reference' => $data['reference'] ?? null,
            'isInitialBalance' => $data['is_initial_balance'] ?? false,
        ]);
    }


    /**
     * Create a MovementsData instance from an EntryData object
     *
     * @param EntryData $entryData
     * @param array $additionalData Additional data to override or complement the entry data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromEntryData(EntryData $entryData, array $additionalData = []): self
    {
        $data = [
            'groupId' => $entryData->groupReceivedId ?? 0,
            'type' => EntryRepository::ENTRY_TYPE,
            'subType' => $entryData->entryType,
            'amount' => $entryData->amount ?? 0.0,
            'description' => $entryData->description ?? 'Entry movement',
            'movementDate' => $entryData->dateEntryRegister ?? '',
            'isInitialBalance' => false,
        ];

        return new self(array_merge($data, $additionalData));
    }




    /**
     * Create a MovementsData instance from an ExitData object
     *
     * @param ExitData $exitData
     * @param array $additionalData Additional data to override or complement the exit data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromExitData(ExitData $exitData, array $additionalData = []): self
    {
        $data = [
            'groupId' => $exitData->group->id ?? 0,
            'type' => ExitRepository::EXIT_TYPE,
            'subType' => $exitData->exitType ?? '',
            'amount' => (float)($exitData->amount ?? 0.0),
            'description' => $exitData->comments ?? 'Exit movement',
            'movementDate' => $exitData->dateExitRegister ?? '',
            'isInitialBalance' => false,
        ];

        // Merge additional data
        return new self(array_merge($data, $additionalData));
    }
}
