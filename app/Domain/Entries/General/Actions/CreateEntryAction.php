<?php

namespace Domain\Entries\General\Actions;

use Domain\Entries\Consolidated\Actions\CreateConsolidatedEntryAction;
use Domain\Entries\Consolidated\Constants\ReturnMessages;
use Domain\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use Domain\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Domain\Entries\General\DataTransferObjects\EntryData;
use Domain\Entries\General\Interfaces\EntryRepositoryInterface;
use Domain\Entries\General\Models\Entry;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Entries\Consolidated\ConsolidatedEntriesRepository;
use Infrastructure\Repositories\Entries\General\EntryRepository;
use Throwable;

class CreateEntryAction
{
    private EntryRepository $entryRepository;
    private ConsolidatedEntriesRepository $consolidatedEntriesRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepositoryInterface,
        CreateConsolidatedEntryAction $createConsolidatedEntryAction,
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
        //$countedNotConsolidateEntry = $this->consolidatedEntriesRepository->getEntriesEvolutionConsolidation('0');

        $this->createConsolidatedEntryAction->__invoke($consolidationEntriesData);
        return $this->entryRepository->newEntry($entryData);

        /*if($countedNotConsolidateEntry->count() == 0)
        {

        }
        else
        {
            throw new GeneralExceptions(
                    ReturnMessages::ERROR_NOT_ALLOW_NEW_ENTRY_WITH_PREVIOUS_MONTHS_NOT_CONSOLIDATE,
                    500);
        }*/
    }
}
