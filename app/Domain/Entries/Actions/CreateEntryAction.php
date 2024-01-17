<?php

namespace Domain\Entries\Actions;

use Domain\Entries\Constants\ReturnMessages;
use Domain\ConsolidationEntries\Actions\CreateConsolidatedEntryAction;
use Domain\ConsolidationEntries\DataTransferObjects\ConsolidationEntriesData;
use Domain\ConsolidationEntries\Interfaces\ConsolidationEntriesRepositoryInterface;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Models\Entry;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\ConsolidationEntries\ConsolidationEntriesRepository;
use Infrastructure\Repositories\Entries\EntryRepository;
use Throwable;

class CreateEntryAction
{
    private EntryRepository $entryRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        CreateConsolidatedEntryAction $createConsolidatedEntryAction,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->createConsolidatedEntryAction = $createConsolidatedEntryAction;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(EntryData $entryData, ConsolidationEntriesData $consolidationEntriesData): Entry
    {
        $this->createConsolidatedEntryAction->__invoke($consolidationEntriesData);
        return $this->entryRepository->newEntry($entryData);
    }
}
