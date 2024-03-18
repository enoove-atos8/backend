<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\Consolidated\Actions\CreateConsolidatedEntryAction;
use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Entries\General\Models\Entry;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
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
