<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Throwable;

class GetEntriesAction
{
    private EntryRepository $entryRepository;
    private GroupsRepository $groupsRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        GroupRepositoryInterface $groupsRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->groupsRepository = $groupsRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(null | string $dates, array $filters, bool $paginate = true): Collection | Paginator
    {
        $entries = $this->entryRepository->getAllEntriesWithMembersAndReviewers(
            $dates,
            'compensated',
            $filters,
            [EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, EntryRepository::ID_COLUMN_JOINED],
            $paginate
        );

        if($entries->count() > 0)
        {
            return $entries;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_ENTRIES_FOUNDED, 404);
        }
    }
}
