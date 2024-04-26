<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\Consolidated\Actions\CreateConsolidatedEntryAction;
use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\General\Constants\ReturnMessages;
use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Entries\General\Models\Entry;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class CreateEntryAction
{
    private EntryRepository $entryRepository;
    private ConsolidationEntriesRepository $consolidatedEntriesRepository;
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
    public function __invoke(EntryData $entryData, ConsolidationEntriesData $consolidationEntriesData): Entry
    {
        $dateEntryRegister = $entryData->dateEntryRegister;
        $dateTransactionCompensation = $entryData->dateTransactionCompensation;

        if($dateTransactionCompensation !== null)
        {
            if(substr($dateEntryRegister, 0, 7) !== substr($dateTransactionCompensation, 0, 7))
                $entryData->dateEntryRegister = substr($dateTransactionCompensation, 0, 7) . '-01';
        }

        $this->createConsolidatedEntryAction->__invoke($consolidationEntriesData);
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
