<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Consolidation\Actions\CreateConsolidatedEntryAction;
use App\Domain\Financial\Entries\Consolidation\Actions\DeleteConsolidatedEntriesAction;
use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Movements\Actions\DeleteMovementByEntryId;
use Domain\Financial\Movements\Actions\DeleteMovementsOfGroupAction;
use Domain\Financial\Movements\Actions\RecalculateBalanceAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class DeleteEntryAction
{
    private EntryRepositoryInterface $entryRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;
    private DeleteConsolidatedEntriesAction $deleteConsolidationEntriesAction;
    private GetEntryByIdAction $getEntryByIdAction;
    private DeleteMovementByEntryId $deleteMovementByEntryId;


    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        CreateConsolidatedEntryAction $createConsolidatedEntryAction,
        DeleteConsolidatedEntriesAction $deleteConsolidationEntriesAction,
        GetEntryByIdAction $getEntryByIdAction,
        DeleteMovementByEntryId $deleteMovementByEntryId,

    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->createConsolidatedEntryAction = $createConsolidatedEntryAction;
        $this->deleteConsolidationEntriesAction = $deleteConsolidationEntriesAction;
        $this->getEntryByIdAction = $getEntryByIdAction;
        $this->deleteMovementByEntryId = $deleteMovementByEntryId;



    }

    /**
     * @param $id
     * @return bool
     * @throws GeneralExceptions|Throwable
     */
    public function execute($id): bool
    {
        $entryDeleted = $this->entryRepository->deleteEntry($id);

        if($entryDeleted)
        {
            $this->deleteMovementByEntryId->execute($id);
            return true;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_ENTRIES_FOUNDED, 500);
        }
    }
}
