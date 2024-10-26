<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\General\Constants\ReturnMessages;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Domain\Members\Actions\GetMembersAction;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Member\MemberRepository;
use Throwable;

class GetAmountByEntryTypeAction
{
    private EntryRepository $entryRepository;
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
    public function __invoke($rangeMonthlyDate, $entryType = 'all'): null | array
    {
        $entries = $this->entryRepository->getAmountByEntryType($rangeMonthlyDate, $entryType);
        $totalTithesAmount = $entries
                            ->where(EntryRepository::ENTRY_TYPE_COLUMN,
                                        BaseRepository::OPERATORS['EQUALS'],
                                EntryRepository::TITHE_VALUE)->sum(EntryRepository::AMOUNT_COLUMN);
        $totalOffersAmount = $entries
            ->where(EntryRepository::ENTRY_TYPE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                EntryRepository::OFFERS_VALUE)->sum(EntryRepository::AMOUNT_COLUMN);

        $totalDesignatedAmount = $entries
            ->where(EntryRepository::ENTRY_TYPE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                EntryRepository::DESIGNATED_VALUE)->sum(EntryRepository::AMOUNT_COLUMN);

        $totalDevolutionAmount = $entries
            ->where(EntryRepository::DEVOLUTION_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                1)->sum(EntryRepository::AMOUNT_COLUMN);


        if(count($entries) > 0)
        {
            return [
                'tithes'        =>  $totalTithesAmount,
                'offers'        =>  $totalOffersAmount,
                'designated'    =>  $totalDesignatedAmount,
                'devolution'    =>  $totalDevolutionAmount,
            ];
        }
        else
        {
            return null;
        }
    }
}
