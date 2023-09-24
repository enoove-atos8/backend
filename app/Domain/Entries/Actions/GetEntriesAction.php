<?php

namespace Domain\Entries\Actions;

use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Entries\EntryRepository;
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
    public function __invoke($request): Collection
    {
        $range = $request->input('dates');
        return $this->entryRepository->getAllEntries($range);
    }
}
