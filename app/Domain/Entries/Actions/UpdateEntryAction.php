<?php

namespace Domain\Entries\Actions;

use Domain\ConsolidationEntries\Actions\CreateConsolidatedEntryAction;
use Domain\ConsolidationEntries\DataTransferObjects\ConsolidationEntriesData;
use Domain\Entries\Constants\ReturnMessages;
use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\EntryRepository;
use Throwable;

class UpdateEntryAction
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
     * @param $id
     * @param EntryData $entryData
     * @param ConsolidationEntriesData $consolidationEntriesData
     * @return bool|mixed
     * @throws GeneralExceptions
     * @throws Throwable
     * @throws BindingResolutionException
     */
    public function __invoke($id, EntryData $entryData, ConsolidationEntriesData $consolidationEntriesData): mixed
    {
        $this->createConsolidatedEntryAction->__invoke($consolidationEntriesData);
        $entry = $this->entryRepository->updateEntry($id, $entryData);

        if($entry)
        {
            return $entry;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_ENTRY, 500);
        }
    }
}
