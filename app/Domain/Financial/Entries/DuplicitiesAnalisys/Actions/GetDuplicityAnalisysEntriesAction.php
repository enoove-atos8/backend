<?php

namespace Domain\Financial\Entries\DuplicitiesAnalisys\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use Illuminate\Support\Collection;

class GetDuplicityAnalisysEntriesAction
{
    private EntryRepositoryInterface $entryRepository;


    public function __construct(EntryRepositoryInterface $entryRepositoryInterface)
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    public function execute(string $date): Collection
    {
        return $this->entryRepository->getDuplicitiesEntries($date);
    }
}
