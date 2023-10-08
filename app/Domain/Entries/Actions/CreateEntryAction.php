<?php

namespace Domain\Entries\Actions;

use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Models\Entry;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\EntryRepository;

class CreateEntryAction
{
    private EntryRepository $entryRepository;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(EntryData $entryData): Entry
    {
        return $this->entryRepository->newEntry($entryData);
    }
}
