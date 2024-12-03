<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
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
    public function __invoke(null | string $dates, array $filters): Collection | Paginator
    {
        $entries = $this->entryRepository->getAllEntriesWithMembersAndReviewers($dates, 'compensated', $filters);

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
