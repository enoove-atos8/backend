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
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Cults\CultRepository;
use Throwable;

class SaveCultAction
{
    private CultRepository $cultRepository;
    private UpdateEntryAction $updateEntryAction;
    private CreateEntryAction $createEntryAction;
    private EntryData $entryData;


    public function __construct(
        CultRepositoryInterface $cultRepositoryInterface,
        UpdateEntryAction $updateEntryAction,
        CreateEntryAction $createEntryAction,
        EntryData $entryData
    )
    {
        $this->cultRepository = $cultRepositoryInterface;
        $this->updateEntryAction = $updateEntryAction;
        $this->createEntryAction = $createEntryAction;
        $this->entryData = $entryData;
    }


    /**
     * @throws Throwable
     */
    public function __invoke($id, CultData $cultData, ConsolidationEntriesData $consolidationEntriesData): void
    {
        $this->calculateTotalTithesAndDesignated($cultData);

        if($id != null)
        {
            $updated = $this->cultRepository->updateCult($id, $cultData);

            if($updated)
            {
                if(count($cultData->tithes) > 0 || count($cultData->designated) > 0 || count($cultData->offers) > 0)
                    $this->updateEntries($id, $cultData, $consolidationEntriesData);
            }
            else
            {
                throw new GeneralExceptions(ReturnMessages::ERROR_CULT_UPDATED, 500);
            }
        }
        else
        {
            $cult = $this->cultRepository->createCult($cultData);

            if($cult->id && !$cultData->worshipWithoutEntries && (count($cultData->tithes) > 0 || count($cultData->designated) > 0 || count($cultData->offers) > 0)){
                $this->createEntries($cult, $cultData, $consolidationEntriesData);
            }
        }
    }



    /**
     * @param Cult $cult
     * @param CultData $cultData
     * @param ConsolidationEntriesData $consolidationEntriesData
     * @return void
     * @throws Throwable
     */
    private function createEntries(Cult $cult, CultData $cultData, ConsolidationEntriesData $consolidationEntriesData): void
    {
        $cultData->id = $cult->id;
        $entries = array_merge($cultData->tithes, $cultData->designated, $cultData->offers);

        foreach ($entries as $entry){
            $this->prepareEntryData($entry);
            $this->transferCultDataToEntryData($cultData);
            $this->createEntryAction->__invoke($this->entryData, $consolidationEntriesData);
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
        $this->entryData->cultId = $cultId;
        $entries = array_merge($cultData->tithes, $cultData->designated, $cultData->offers);

        foreach ($entries as $entry){
            $this->prepareEntryData($entry);
            $this->transferCultDataToEntryData($cultData);

            if(array_key_exists('id', $entry))
                $this->updateEntryAction->__invoke($entry['id'], $this->entryData, $consolidationEntriesData);
            else
                $this->createEntryAction->__invoke($this->entryData, $consolidationEntriesData);
        }
    }



    /**
     * @param array|CultData $entry
     * @return void
     */
    private function prepareEntryData(array | CultData $entry): void
    {
        if($entry instanceof CultData)
            $this->entryData->entryType = EntryRepository::OFFERS_VALUE;
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
        else if($this->entryData->entryType == 'offers'){
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
        $this->entryData->reviewerId = $cultData->reviewerId;
        $this->entryData->transactionType = $cultData->transactionType;
        $this->entryData->transactionCompensation = $cultData->transactionCompensation;
        $this->entryData->dateTransactionCompensation = $cultData->dateTransactionCompensation;
        $this->entryData->deleted = $cultData->deleted;
        $this->entryData->receipt = $cultData->receipt;
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

        if (!is_null($cultData->offers)) {
            foreach ($cultData->offers as $offer) {
                $cultData->amountOffers += $offer['amount'];
            }
        }
    }
}
