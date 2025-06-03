<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Consolidation\Actions\CreateConsolidatedEntryAction;
use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Models\Entry;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Financial\Movements\Actions\CreateMovementAction;
use Domain\Financial\Movements\Actions\GetCurrentBalanceAction;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Movements\MovementRepository;
use Throwable;

class CreateEntryAction
{
    private EntryRepositoryInterface $entryRepository;
    private ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;
    private CreateMovementAction $createMovementAction;
    private MovementsData $movementsData;
    private MovementRepositoryInterface $movementRepository;
    private GroupRepositoryInterface $groupRepository;

    public function __construct(
        EntryRepositoryInterface               $entryRepositoryInterface,
        ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepositoryInterface,
        CreateConsolidatedEntryAction          $createConsolidatedEntryAction,
        CreateMovementAction                   $createMovementAction,
        MovementsData                          $movementsData,
        MovementRepositoryInterface            $movementRepositoryInterface,
        GroupRepositoryInterface               $groupRepositoryInterface
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->consolidatedEntriesRepository = $consolidatedEntriesRepositoryInterface;
        $this->createConsolidatedEntryAction = $createConsolidatedEntryAction;
        $this->createMovementAction = $createMovementAction;
        $this->movementsData = $movementsData;
        $this->movementRepository = $movementRepositoryInterface;
        $this->groupRepository = $groupRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(EntryData $entryData, ConsolidationEntriesData $consolidationEntriesData): ?Entry
    {
        $dateEntryRegister = $entryData->dateEntryRegister;
        $dateTransactionCompensation = $entryData->dateTransactionCompensation;

        if($dateTransactionCompensation !== null)
        {
            if(substr($dateEntryRegister, 0, 7) !== substr($dateTransactionCompensation, 0, 7))
                $entryData->dateEntryRegister = substr($dateTransactionCompensation, 0, 7) . '-01';
        }

        $this->createConsolidatedEntryAction->execute($consolidationEntriesData);
        $entry = $this->entryRepository->newEntry($entryData);
        $entryData->id = $entry->id;

        if(!is_null($entryData->id))
        {
            if($entryData->entryType == EntryRepository::DESIGNATED_VALUE)
            {
                $group = $this->groupRepository->getGroupById($entryData->groupReceivedId);

                if($group && $group->financialMovement)
                {
                    $movementData = $this->movementsData::fromObjectData($entryData);
                    $this->createMovementAction->execute($movementData);
                }
            }

            return $entry;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_ENTRY, 500);
        }
    }
}
