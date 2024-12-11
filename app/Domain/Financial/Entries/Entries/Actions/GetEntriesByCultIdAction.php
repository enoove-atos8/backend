<?php

namespace Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Illuminate\Support\Collection;
use Throwable;

class GetEntriesByCultIdAction
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
    public function __invoke(int $id): Collection
    {
        return $this->entryRepository->getEntriesByCultId($id);
    }
}
