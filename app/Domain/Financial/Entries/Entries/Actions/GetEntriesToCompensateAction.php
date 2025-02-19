<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Throwable;

class GetEntriesToCompensateAction
{
    private EntryRepository $entryRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(): Paginator
    {
        return $this->entryRepository->getAllEntriesWithMembersAndReviewers(
            'all',
            EntryRepository::TO_COMPENSATE_VALUE,
            [],
            [EntryRepository::DATE_ENTRY_REGISTER_COLUMN_JOINED]);
    }
}
