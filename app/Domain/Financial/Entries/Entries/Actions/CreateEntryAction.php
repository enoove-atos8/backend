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
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class CreateEntryAction
{
    private EntryRepository $entryRepository;
    private ConsolidationRepository $consolidatedEntriesRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;

    public function __construct(
        EntryRepositoryInterface                $entryRepositoryInterface,
        ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepositoryInterface,
        CreateConsolidatedEntryAction           $createConsolidatedEntryAction,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->consolidatedEntriesRepository = $consolidatedEntriesRepositoryInterface;
        $this->createConsolidatedEntryAction = $createConsolidatedEntryAction;
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
            return $entry;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_CREATE_ENTRY, 500);
        }
    }
}
