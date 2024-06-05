<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\Consolidated\Actions\CreateConsolidatedEntryAction;
use App\Domain\Financial\Entries\Consolidated\Actions\DeleteConsolidatedEntriesAction;
use App\Domain\Financial\Entries\General\Constants\ReturnMessages;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class DeleteEntryAction
{
    private EntryRepository $entryRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;
    private DeleteConsolidatedEntriesAction $deleteConsolidationEntriesAction;
    private GetEntryByIdAction $getEntryByIdAction;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        CreateConsolidatedEntryAction $createConsolidatedEntryAction,
        DeleteConsolidatedEntriesAction $deleteConsolidationEntriesAction,
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
