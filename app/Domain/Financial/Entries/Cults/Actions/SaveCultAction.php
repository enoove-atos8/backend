<?php

namespace Domain\Financial\Entries\Cults\Actions;

use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Cults\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Cults\DataTransferObjects\CultData;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Cults\Models\Cult;
use App\Domain\Financial\Entries\Entries\Actions\CreateEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateEntryAction;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Entries\Entries\Actions\GetEntriesByCultIdAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Cults\CultRepository;
use Throwable;

class SaveCultAction
{
    private CultRepositoryInterface $cultRepository;
    private UpdateEntryAction $updateEntryAction;
    private CreateEntryAction $createEntryAction;
    private GetEntriesByCultIdAction $getEntriesByCultIdAction;
    private EntryData $entryData;


    public function __construct(
        CultRepositoryInterface $cultRepositoryInterface,
        UpdateEntryAction $updateEntryAction,
        CreateEntryAction $createEntryAction,
        GetEntriesByCultIdAction $getEntriesByCultIdAction,
        EntryData $entryData
    )
    {
        $this->cultRepository = $cultRepositoryInterface;
        $this->updateEntryAction = $updateEntryAction;
        $this->createEntryAction = $createEntryAction;
        $this->getEntriesByCultIdAction = $getEntriesByCultIdAction;
        $this->entryData = $entryData;
    }


    /**
     * @throws Throwable
     */
    public function execute($id, CultData $cultData, ConsolidationEntriesData $consolidationEntriesData): void
    {
        $this->calculateTotalTithesAndDesignated($cultData);

        if($id != null)
        {
            $updated = $this->cultRepository->updateCult($id, $cultData);
            $cultData->id = $id;

            if($updated)
            {
                if(count($cultData->tithes) > 0 || count($cultData->designated) > 0 || count($cultData->offer) > 0)
                {
                    $entriesForTheCult = $this->getEntriesByCultIdAction->execute($id);

                    if(count($entriesForTheCult) > 0)
                        $this->updateEntries($id, $cultData, $consolidationEntriesData);
                    else
                        $this->createEntries($id, $cultData, $consolidationEntriesData);
                }
            }
            else
            {
                throw new GeneralExceptions(ReturnMessages::ERROR_CULT_UPDATED, 500);
            }
        }
        else
        {
            $cult = $this->cultRepository->createCult($cultData);

            if($cult->id && !$cultData->worshipWithoutEntries && (count($cultData->tithes) > 0 || count($cultData->designated) > 0 || count($cultData->offer) > 0)){
                $this->createEntries($cult->id, $cultData, $consolidationEntriesData);
            }
        }
    }


    /**
     * @param $cultId
     * @param CultData $cultData
     * @param ConsolidationEntriesData $consolidationEntriesData
     * @return void
     * @throws Throwable
     */
    private function createEntries($cultId, CultData $cultData, ConsolidationEntriesData $consolidationEntriesData): void
    {
        $cultData->id = $cultId;
        $entries = array_merge($cultData->tithes, $cultData->designated, $cultData->offer);

        foreach ($entries as $entry)
        {
            if(!is_null($entry['amount']))
            {
                $this->prepareEntryData($entry);
                $this->transferCultDataToEntryData($cultData);
                $this->createEntryAction->execute($this->entryData, $consolidationEntriesData);
            }
        }
    }




    /**
     * @param $cultId
     * @param CultData $cultData
     * @param ConsolidationEntriesData $consolidationEntriesData
     * @return void
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     * @throws Throwable
     */
    private function updateEntries($cultId, CultData $cultData, ConsolidationEntriesData $consolidationEntriesData): void
    {
        $entries = array_merge($cultData->tithes, $cultData->designated, $cultData->offer);

        foreach ($entries as $entry){
            $this->prepareEntryData($entry);
            $this->transferCultDataToEntryData($cultData);

            if(array_key_exists('id', $entry))
                $this->updateEntryAction->execute($entry['id'], $this->entryData, $consolidationEntriesData);
            else
                $this->createEntryAction->execute($this->entryData, $consolidationEntriesData);
        }
    }



    /**
     * @param array|CultData $entry
     * @return void
     */
    private function prepareEntryData(array | CultData $entry): void
    {
        if($entry instanceof CultData)
            $this->entryData->entryType = EntryRepository::OFFER_VALUE;
        else
            $this->entryData->entryType = $entry['entryType'];


        if($this->entryData->entryType == 'designated'){
            $this->entryData->groupReceivedId = $entry['groupReceivedId'];
            $this->entryData->amount = $entry['amount'];
        }
        else if($this->entryData->entryType == 'tithes'){
            $this->entryData->memberId = $entry['memberId'];
            $this->entryData->amount = $entry['amount'];
        }
        else if($this->entryData->entryType == 'offer'){
            $this->entryData->memberId = null;
            $this->entryData->groupReceivedId = null;
            $this->entryData->amount = $entry['amount'];
        }
    }



    /**
     * @param CultData $cultData
     * @return void
     */
    private function transferCultDataToEntryData(CultData $cultData): void
    {
        $currentDate = date('Y-m-d');

        $this->entryData->cultId = $cultData->id;
        $this->entryData->reviewerId = $cultData->reviewerId;
        $this->entryData->transactionType = $cultData->transactionType;
        $this->entryData->transactionCompensation = $cultData->transactionCompensation;
        $this->entryData->dateEntryRegister = $currentDate;
        $this->entryData->dateTransactionCompensation = $cultData->dateTransactionCompensation;
        $this->entryData->deleted = $cultData->deleted;
        $this->entryData->receipt = $cultData->receipt;
        $this->entryData->accountId = $cultData->accountId;
        $this->entryData->devolution = 0;
    }



    /**
     * @param CultData $cultData
     * @return void
     */
    private function calculateTotalTithesAndDesignated(CultData $cultData): void
    {
        if (!is_null($cultData->tithes)) {
            foreach ($cultData->tithes as $tithe) {
                $cultData->amountTithes += $tithe['amount'];
            }
        }

        if (!is_null($cultData->designated)) {
            foreach ($cultData->designated as $designated) {
                $cultData->amountDesignated += $designated['amount'];
            }
        }

        if (!is_null($cultData->offer)) {
            foreach ($cultData->offer as $offer) {
                $cultData->amountOffer += $offer['amount'];
            }
        }
    }
}
