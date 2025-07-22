<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Secretary\Membership\Actions\GetMembersAction;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class GetAmountByEntryTypeAction
{
    private EntryRepositoryInterface $entryRepository;
    private GetMembersAction $getMembersAction;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        GetMembersAction $getMembersAction,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->getMembersAction = $getMembersAction;
    }

    /**
     * @throws Throwable
     */
    public function execute($rangeMonthlyDate, $entryType = 'all'): null | array
    {
        $entries = $this->entryRepository->getAmountByEntryType($rangeMonthlyDate, $entryType);
        $totalTithesAmount = $entries
                            ->where(EntryRepository::ENTRY_TYPE_COLUMN,
                                        BaseRepository::OPERATORS['EQUALS'],
                                EntryRepository::TITHE_VALUE)->sum(EntryRepository::AMOUNT_COLUMN);
        $totalOfferAmount = $entries
            ->where(EntryRepository::ENTRY_TYPE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                EntryRepository::OFFER_VALUE)->sum(EntryRepository::AMOUNT_COLUMN);

        $totalDesignatedAmount = $entries
            ->where(EntryRepository::ENTRY_TYPE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                EntryRepository::DESIGNATED_VALUE)->sum(EntryRepository::AMOUNT_COLUMN);

        $totalDevolutionAmount = $entries
            ->where(EntryRepository::DEVOLUTION_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                1)->sum(EntryRepository::AMOUNT_COLUMN);


        return [
            'tithes'        =>  $totalTithesAmount,
            'offer'         =>  $totalOfferAmount,
            'designated'    =>  $totalDesignatedAmount,
            'devolution'    =>  $totalDevolutionAmount,
        ];
    }
}
