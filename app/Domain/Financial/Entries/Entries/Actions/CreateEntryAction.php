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
use Domain\Financial\Movements\Actions\CreateEntryMovementAction;
use Domain\Financial\Movements\Actions\GetCurrentBalanceAction;
use Domain\Financial\Movements\DataTransferObjects\MovementsData;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class CreateEntryAction
{
    private EntryRepositoryInterface $entryRepository;
    private ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;
    private CreateEntryMovementAction $createEntryMovementAction;
    private MovementsData $movementsData;
    private MovementRepositoryInterface $movementRepository;

    public function __construct(
        EntryRepositoryInterface                $entryRepositoryInterface,
        ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepositoryInterface,
        CreateConsolidatedEntryAction           $createConsolidatedEntryAction,
        CreateEntryMovementAction               $createEntryMovementAction,
        MovementsData                           $movementsData,
        MovementRepositoryInterface             $movementRepositoryInterface
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->consolidatedEntriesRepository = $consolidatedEntriesRepositoryInterface;
        $this->createConsolidatedEntryAction = $createConsolidatedEntryAction;
        $this->createEntryMovementAction = $createEntryMovementAction;
        $this->movementsData = $movementsData;
        $this->movementRepository = $movementRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(EntryData $entryData, ConsolidationEntriesData $consolidationEntriesData): Entry
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

        if($entry->id !== null)
        {
            // Get the current balance from the database
            //$currentBalance = 0.0;
            /*$movements = $this->movementRepository->getMovementsByGroup($entryData->groupReceivedId);

            if (!$movements->isEmpty()) {
                // Get the last movement to get the current balance
                $lastMovement = $movements->sortByDesc('movementDate')->first();
                $currentBalance = $lastMovement->balance;
            }

            // Create a movement record for this entry with the current balance
            $movementData = $this->movementsData::fromEntryData($entryData, [
                'referenceId' => $entry->id,
                'balance' => $currentBalance // Pass the current balance from the database
            ]);

            $this->createEntryMovementAction->execute($movementData);*/

            return $entry;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_ENTRY, 500);
        }
    }
}
