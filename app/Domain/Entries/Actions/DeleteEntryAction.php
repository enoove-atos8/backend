<?php

namespace Domain\Entries\Actions;

use Domain\ConsolidationEntries\Actions\CreateConsolidatedEntryAction;
use Domain\ConsolidationEntries\Actions\DeleteConsolidationEntriesAction;
use Domain\Entries\Constants\ReturnMessages;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\EntryRepository;
use Throwable;

class DeleteEntryAction
{
    private EntryRepository $entryRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;
    private DeleteConsolidationEntriesAction $deleteConsolidationEntriesAction;
    private GetEntryByIdAction $getEntryByIdAction;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        CreateConsolidatedEntryAction $createConsolidatedEntryAction,
        DeleteConsolidationEntriesAction $deleteConsolidationEntriesAction,
        GetEntryByIdAction $getEntryByIdAction,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->createConsolidatedEntryAction = $createConsolidatedEntryAction;
        $this->deleteConsolidationEntriesAction = $deleteConsolidationEntriesAction;
        $this->getEntryByIdAction = $getEntryByIdAction;

    }

    /**
     * @param $id
     * @return bool
     * @throws GeneralExceptions|Throwable
     */
    public function __invoke($id): bool
    {
        $entryDeleted = $this->entryRepository->deleteEntry($id);

        if($entryDeleted)
            return true;
        else
            throw new GeneralExceptions(ReturnMessages::INFO_NO_ENTRIES_FOUNDED, 500);
    }
}
