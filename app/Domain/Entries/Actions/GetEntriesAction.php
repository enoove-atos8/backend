<?php

namespace App\Domain\Entries\Actions;

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
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        return $this->entryRepository->getAllEntries($startDate, $endDate);
    }
}
