<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\General\Constants\ReturnMessages;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Domain\Members\Actions\GetMembersAction;
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
    public function __invoke($rangeMonthlyDate, $amountType, $entryType): object
    {
        $amountByEntryType = new class{};

        $entries = $this->entryRepository->getAmountByEntryType($rangeMonthlyDate, $amountType, $entryType);
        $allEntries = $this->entryRepository->getAllEntries($rangeMonthlyDate)->count();
        $totalMembers = $this->getMembersAction->__invoke()->where(MemberRepository::ACTIVATED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 1)
                                                         ->count();
        $qtdEntriesByMembers = $entries->where(EntryRepository::MEMBER_ID_COLUMN, BaseRepository::OPERATORS['DIFFERENT'], null)
                                      ->groupBy(EntryRepository::MEMBER_ID_COLUMN)
                                      ->count();

        $totalGeneral = $entries->sum(EntryRepository::AMOUNT_COLUMN);

        $amountByEntryType->entryType = $entryType;
        $amountByEntryType->amountType = $amountType;
        $amountByEntryType->totalGeneral = $totalGeneral;
        $amountByEntryType->qtdTithingMembers = null;

        if($entryType == EntryRepository::TITHE_VALUE){
            $amountByEntryType->qtdTithes = $entries->count();
            $amountByEntryType->proportionEntriesTithes = $entries->count() > 0 ? $entries->count() / $allEntries : 0;
            $amountByEntryType->qtdTithingMembers = $qtdEntriesByMembers;
            $amountByEntryType->proportionEntriesTithesMembers = $qtdEntriesByMembers / $totalMembers;
        }

        elseif ($entryType == EntryRepository::OFFERS_VALUE){
            $amountByEntryType->qtdOffers = $entries->count();
            $amountByEntryType->proportionEntriesOffers = $entries->count() > 0 ? $entries->count() / $allEntries : 0;
            $amountByEntryType->offersDoNotIdentified = 0;
        }

        elseif ($entryType == EntryRepository::DESIGNATED_VALUE){
            $entriesDesignatedDevolution = $entries->where(EntryRepository::DEVOLUTION_COLUMN, BaseRepository::OPERATORS['EQUALS'], 1);
            $amountByEntryType->qtdDesignated = $entries->count();
            $amountByEntryType->proportionEntriesDesignated = $entries->count() > 0 ? $entries->count() / $allEntries : 0;
            $amountByEntryType->designatedOfDevolutions = $entriesDesignatedDevolution->sum(EntryRepository::AMOUNT_COLUMN);
        }

        return $amountByEntryType;
    }
}
