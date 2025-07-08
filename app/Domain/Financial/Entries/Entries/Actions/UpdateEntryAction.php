<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Consolidation\Actions\CreateConsolidatedEntryAction;
use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Models\Entry;
use App\Domain\Financial\Movements\Actions\GetMovementByEntryIdAction;
use App\Domain\Financial\Movements\Actions\UpdateMovementAmountAction;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Movements\Actions\RecalculateBalanceAction;
use Domain\Financial\Movements\Actions\UpdateMovementBalanceAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class UpdateEntryAction
{
    private EntryRepositoryInterface $entryRepository;
    private CreateConsolidatedEntryAction $createConsolidatedEntryAction;
    private GetEntryByIdAction $getEntryByIdAction;
    private UpdateMovementAmountAction $updateMovementAmountAction;
    private RecalculateBalanceAction $recalculateBalanceAction;
    private GetMovementByEntryIdAction $getMovementByEntryIdAction;

    public function __construct(
        EntryRepositoryInterface      $entryRepositoryInterface,
        CreateConsolidatedEntryAction $createConsolidatedEntryAction,
        GetEntryByIdAction $getEntryByIdAction,
        UpdateMovementAmountAction $updateMovementAmountAction,
        RecalculateBalanceAction $recalculateBalanceAction,
        GetMovementByEntryIdAction $getMovementByEntryIdAction
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->createConsolidatedEntryAction = $createConsolidatedEntryAction;
        $this->getEntryByIdAction = $getEntryByIdAction;
        $this->updateMovementAmountAction = $updateMovementAmountAction;
        $this->recalculateBalanceAction = $recalculateBalanceAction;
        $this->getMovementByEntryIdAction = $getMovementByEntryIdAction;

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
    public function execute($id, EntryData $entryData, ConsolidationEntriesData $consolidationEntriesData): mixed
    {
        $previousEntry = $this->getEntryByIdAction->execute($id);
        $dateEntryRegister = $entryData->dateEntryRegister;
        $dateTransactionCompensation = $entryData->dateTransactionCompensation;

        if($dateTransactionCompensation !== null)
        {
            if(substr($dateEntryRegister, 0, 7) !== substr($dateTransactionCompensation, 0, 7))
                $entryData->dateEntryRegister = substr($dateTransactionCompensation, 0, 7) . '-01';
        }

        $this->createConsolidatedEntryAction->execute($consolidationEntriesData);
        $entry = $this->entryRepository->updateEntry($id, $entryData);

        if($entry)
        {
            if($entryData->entryType == EntryRepository::DESIGNATED_VALUE)
            {
                if($previousEntry->amount !== $entryData->amount)
                    $this->updateMovementAndRecalculateBalance($previousEntry, $entryData->amount);

            }
            return $entry;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_ENTRY, 500);
        }
    }


    /**
     * Atualiza o movimento e recalcula o saldo.
     *
     * @param Model $originalEntry
     * @param float $newAmount
     * @return void
     */
    private function updateMovementAndRecalculateBalance(Model $originalEntry, float $newAmount): void
    {
        $movement = $this->getMovementByEntryIdAction->execute($originalEntry->id);

        if (!is_null($movement->id))
        {
            $this->updateMovementAmountAction->execute($movement->id, $newAmount);
            $this->recalculateBalanceAction->execute($movement->groupId);
        }
    }

}
