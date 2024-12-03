<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Throwable;

class GetDevolutionEntriesAction
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
    public function __invoke(string|null $date): Collection
    {
        return $this->entryRepository->getDevolutionEntries($date);
    }
}
