<?php

namespace Domain\Entries\Interfaces;

use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Models\Entry;
use Illuminate\Support\Collection;
use Domain\Churches\Models\Tenant;
use Infrastructure\Repositories\Entries\EntryRepository;

interface EntryRepositoryInterface
{
    public function newEntry(EntryData $entryData): Entry;

    public function getAllEntries(string $startDate, string $endDate): Collection;
}
