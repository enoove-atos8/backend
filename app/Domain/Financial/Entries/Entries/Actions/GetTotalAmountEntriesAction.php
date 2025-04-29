<?php

namespace Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class GetTotalAmountEntriesAction
{
    private EntryRepositoryInterface $entryRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(null | string $dates): array
    {
        $entries = $this->entryRepository->getAllEntriesWithMembersAndReviewers(
            $dates,
            'compensated',
            [],
            [EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, EntryRepository::ID_COLUMN_JOINED],
            false);

        $amountTithes = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, EntryRepository::TITHE_VALUE)->sum(EntryRepository::AMOUNT_COLUMN_JOINED_WITH_UNDERLINE);
        $amountOffer = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, EntryRepository::OFFER_VALUE)->sum(EntryRepository::AMOUNT_COLUMN_JOINED_WITH_UNDERLINE);
        $amountDesignated = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, EntryRepository::DESIGNATED_VALUE)->sum(EntryRepository::AMOUNT_COLUMN_JOINED_WITH_UNDERLINE);

        if($entries->count() > 0)
        {
            return [
                'totalAmount'   =>  [
                    'titheAmount'       =>  $amountTithes,
                    'offerAmount'      =>  $amountOffer,
                    'designatedAmount'  =>  $amountDesignated,
                ]
            ];
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_ENTRIES_FOUNDED, 404);
        }
    }
}
