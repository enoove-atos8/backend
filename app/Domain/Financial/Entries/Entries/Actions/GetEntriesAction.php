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
    private EntryRepositoryInterface $entryRepository;
    private GroupRepositoryInterface $groupsRepository;

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
    public function execute(null | string $dates, array $filters, bool $paginate = true): Collection | Paginator
    {
        return $this->entryRepository->getAllEntriesWithMembersAndReviewers(
            $dates,
            'compensated',
            $filters,
            [EntryRepository::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, EntryRepository::ID_COLUMN_JOINED],
            $paginate
        );
    }
}
