<?php

namespace Domain\Entries\General\Actions;

use Domain\Entries\General\Constants\ReturnMessages;
use Domain\Entries\General\Interfaces\EntryRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\General\EntryRepository;
use Throwable;

class GetEntriesAction
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
    public function __invoke(string $dates): Collection
    {
        $entries = $this->entryRepository->getAllEntries($dates);

        if($entries->count() !== 0)
        {
            return $entries;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_ENTRIES_FOUNDED, 404);
        }
    }
}
