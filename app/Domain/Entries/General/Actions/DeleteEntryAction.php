<?php

namespace Domain\Entries\General\Actions;

use Domain\Entries\Consolidated\Actions\CreateConsolidatedEntryAction;
use Domain\Entries\Consolidated\Actions\DeleteConsolidatedEntriesAction;
use Domain\Entries\General\Constants\ReturnMessages;
use Domain\Entries\General\Interfaces\EntryRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\General\EntryRepository;
use Throwable;

class DeleteEntryAction
{
    private EntryRepository $entryRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;
    private DeleteConsolidatedEntriesAction $deleteConsolidationEntriesAction;
    private GetEntryByIdAction $getEntryByIdAction;

    public function __construct(
        EntryRepositoryInterface        $entryRepositoryInterface,
        CreateConsolidatedEntryAction   $createConsolidatedEntryAction,
        DeleteConsolidatedEntriesAction $deleteConsolidationEntriesAction,
        GetEntryByIdAction              $getEntryByIdAction,
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
