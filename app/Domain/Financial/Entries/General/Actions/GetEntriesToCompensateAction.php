<?php

namespace App\Domain\Financial\Entries\General\Actions;

use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
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
    public function __invoke(): Collection
    {
        return $this->entryRepository->getAllEntriesWithMembersAndReviewers(
            'all',
            EntryRepository::TO_COMPENSATE_VALUE,
            'entries.date_entry_register');
    }
}
