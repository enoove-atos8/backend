<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;


use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;

class UpdateAmountConsolidatedEntriesAction
{
    private ConsolidationEntriesRepository $consolidationEntriesRepository;
    private EntryRepository $entryRepository;


    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface,
        EntryRepositoryInterface                $entryRepositoryInterface
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
        $this->entryRepository = $entryRepositoryInterface;
    }


    /**
     * @param string $date
     * @return bool
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke(string $date): bool
    {
        $amountUpdated = false;
        $allEntriesByDate = $this->entryRepository
                            ->getAllEntriesByDateAndType($date, 'transaction');

        $totalAmount = $allEntriesByDate->sum(EntryRepository::AMOUNT_COLUMN);

        $amountTithe = $allEntriesByDate
                        ->where(EntryRepository::ENTRY_TYPE_COLUMN,
                                    BaseRepository::OPERATORS['EQUALS'],
                                    EntryRepository::TITHE_VALUE)
                        ->sum(EntryRepository::AMOUNT_COLUMN);

        $amountDesignated = $allEntriesByDate
                        ->where(EntryRepository::ENTRY_TYPE_COLUMN,
                                      BaseRepository::OPERATORS['EQUALS'],
                                EntryRepository::DESIGNATED_VALUE)
                        ->sum(EntryRepository::AMOUNT_COLUMN);

        $amountOffers = $allEntriesByDate
                        ->where(EntryRepository::ENTRY_TYPE_COLUMN,
                                    BaseRepository::OPERATORS['EQUALS'],
                              EntryRepository::OFFERS_VALUE)
                        ->sum(EntryRepository::AMOUNT_COLUMN);


        if($amountTithe != 0)
            $amountUpdated = $this->consolidationEntriesRepository
                ->updateTotalValueConsolidation($date, $amountTithe, ConsolidationEntriesRepository::AMOUNT_TITHE_COLUMN);

        if($amountDesignated != 0)
            $amountUpdated = $this->consolidationEntriesRepository
                ->updateTotalValueConsolidation($date, $amountDesignated, ConsolidationEntriesRepository::AMOUNT_DESIGNATED_COLUMN);

        if($amountOffers != 0)
            $amountUpdated = $this->consolidationEntriesRepository
                ->updateTotalValueConsolidation($date, $amountOffers, ConsolidationEntriesRepository::AMOUNT_OFFERS_COLUMN);


        $this->consolidationEntriesRepository
            ->updateTotalValueConsolidation($date, $totalAmount, ConsolidationEntriesRepository::AMOUNT_TOTAL_COLUMN);

        if($amountUpdated)
            return true;
        else
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_ENTRIES_CONSOLIDATED, 500);
    }

}
