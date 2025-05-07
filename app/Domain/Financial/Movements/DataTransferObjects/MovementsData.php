<?php

namespace Domain\Financial\Movements\DataTransferObjects;

use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Exception;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MovementsData extends DataTransferObject
{
    /** @var int|null */
    public ?int $id;

    /** @var int|null */
    public ?int $groupId;

    /** @var int|null */
    public ?int $entryId;

    /** @var int|null */
    public ?int $exitId;

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

    /** @var bool|null */
    public ?bool $isInitialBalance;

    /** @var bool|null */
    public ?bool $deleted;

    /**
     * @throws UnknownProperties
     */
    public static function fromResponse(array $data): self
    {
        return new self([
            'groupId' => $data['group_id'] ?? 0,
            'entryId' => $data['entry_id'] ?? 0,
            'exitId' => $data['exit_id'] ?? 0,
            'type' => $data['type'] ?? null,
            'subType' => $data['sub_type'] ?? null,
            'amount' => $data['amount'] ?? 0.0,
            'balance' => $data['balance'] ?? 0.0,
            'description' => $data['description'] ?? null,
            'movementDate' => $data['movement_date'] ?? '',
            'reference' => $data['reference'] ?? null,
            'isInitialBalance' => $data['is_initial_balance'] ?? false,
            'deleted' => $data['deleted'] ?? false,
        ]);
    }


    /**
     * Create a MovementsData instance from either an EntryData or ExitData object
     *
     * @param EntryData|null $entryData Entry data object (optional)
     * @param ExitData|null $exitData Exit data object (optional)
     * @param array $additionalData Additional data to override or complement the data
     * @return self
     * @throws UnknownProperties
     * @throws Exception If neither EntryData nor ExitData is provided
     */
    public static function fromObjectData(?EntryData $entryData = null, ?ExitData $exitData = null, array $additionalData = []): self
    {

        if ($entryData === null && $exitData === null && empty($additionalData))
            throw new GeneralExceptions("Não foi informado nem um obejeto EntryData e nem um ExitData, verifique!", 500);

        $isEntry = $entryData !== null;

        $data = [
            'groupId' => $isEntry ? ($entryData->groupReceivedId ?? 0) : ($exitData->group->id ?? 0),
            'entryId' => $isEntry ? ($entryData->id ?? null) : null,
            'exitId' => $isEntry ? null : ($exitData->id ?? null),
            'type' => $isEntry ? EntryRepository::ENTRY_TYPE : ExitRepository::EXIT_TYPE,
            'subType' => $isEntry ? ($entryData->entryType ?? '') : ($exitData->exitType ?? ''),
            'amount' => $isEntry ? ($entryData->amount ?? 0.0) : (float)($exitData->amount ?? 0.0),
            'description' => $isEntry ? ($entryData->comments ?? 'Entry movement') : ($exitData->comments ?? 'Exit movement'),
            'movementDate' => $isEntry ? ($entryData->dateEntryRegister ?? '') : ($exitData->dateExitRegister ?? ''),
            'isInitialBalance' => false,
            'deleted' => false,
        ];

        // Merge additional data and return new instance
        return new self(array_merge($data, $additionalData));
    }

    /**
     * Create a MovementsData instance from a GroupData object (specifically for initial balance)
     *
     * @param GroupData $groupData Group data object
     * @param float $totalPreviousMovements Total amount from previous movements (optional)
     * @param array $additionalData Additional data to override or complement the data
     * @return self
     * @throws UnknownProperties
     */
    public static function fromGroupData(GroupData $groupData, float $totalPreviousMovements = 0.0, array $additionalData = []): self
    {
        // Calcular o saldo inicial considerando movimentações anteriores
        $initialBalance = !is_null($groupData->initialBalance)
            ? (float)$groupData->initialBalance + $totalPreviousMovements
            : $totalPreviousMovements;

        $data = [
            'groupId' => $groupData->id,
            'entryId' => null,
            'exitId' => null,
            'type' => null,
            'subType' => null,
            'amount' => $initialBalance,
            'balance' => null,
            'description' => 'Saldo Inicial do Grupo',
            'movementDate' => now()->format('Y-m-d'),
            'isInitialBalance' => true,
            'deleted' => false,
        ];

        // Merge additional data and return new instance
        return new self(array_merge($data, $additionalData));
    }
}
