<?php

namespace Domain\Financial\Entries\Cults\Actions;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Cults\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Cults\DataTransferObjects\CultData;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Cults\Models\Cult;
use App\Domain\Financial\Entries\General\Actions\CreateEntryAction;
use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Cults\CultRepository;
use Throwable;

class CreateCultAction
{
    private CultRepository $cultRepository;
    private CreateEntryAction $createEntryAction;
    private EntryData $entryData;


    public function __construct(
        CultRepositoryInterface $cultRepositoryInterface,
        CreateEntryAction $createEntryAction,
        EntryData $entryData
    )
    {
        $this->cultRepository = $cultRepositoryInterface;
        $this->createEntryAction = $createEntryAction;
        $this->entryData = $entryData;
    }



    /**
     * @throws Throwable
     */
    public function __invoke(CultData $cultData, ConsolidationEntriesData $consolidationEntriesData): Cult
    {
        if(is_null($cultData->offers) && count($cultData->tithes) == 0 && count($cultData->designated) == 0)
        {
            throw new GeneralExceptions(ReturnMessages::AT_LEAST_ONE_ENTRY, 500);
        }
        else
        {
            $this->calculateTotalTithesAndDesignated($cultData);

            $cult = $this->createCult($cultData);

            if ($cult->id) {
                $this->createEntries($cult, $cultData, $consolidationEntriesData);
            }

            return $cult;
        }

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
            $cultData->amountOffers = $cultData->offers;
        }
    }


    /**
     * @param CultData $cultData
     * @return Cult
     */
    private function createCult(CultData $cultData): Cult
    {
        return $this->cultRepository->createCult($cultData);
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

        if(count($cultData->tithes) > 0 || count($cultData->designated) > 0){
            $entries = array_merge($cultData->tithes, $cultData->designated);

            foreach ($entries as $entry){
                $this->prepareEntryData($entry);
                $this->transferCultDataToEntryData($cultData);
                $this->createEntryAction->__invoke($this->entryData, $consolidationEntriesData);
            }
        }

        if(!is_null($cultData->offers)){
            $this->prepareEntryData($cultData);
            $this->transferCultDataToEntryData($cultData);

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
        else if($this->entryData->entryType == 'tithe'){
            $this->entryData->memberId = $entry['memberId'];
            $this->entryData->amount = $entry['amount'];
        }
        else if($this->entryData->entryType == 'offers'){
            $this->entryData->memberId = null;
            $this->entryData->groupReceivedId = null;
            $this->entryData->amount = $entry->offers;
        }



    }


    /**
     * @param CultData $cultData
     * @return void
     */
    private function transferCultDataToEntryData(CultData $cultData): void
    {
        $this->entryData->reviewerId = $cultData->reviewerId;
        $this->entryData->cultId = $cultData->id;
        $this->entryData->transactionType = $cultData->transactionType;
        $this->entryData->transactionCompensation = $cultData->transactionCompensation;
        $this->entryData->dateTransactionCompensation = $cultData->dateTransactionCompensation;
        $this->entryData->dateEntryRegister = $cultData->cultDay;
        $this->entryData->deleted = $cultData->deleted;
        $this->entryData->receipt = $cultData->receipt;
        $this->entryData->devolution = 0;
    }
}
