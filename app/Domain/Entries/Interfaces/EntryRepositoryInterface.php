<?php

namespace Domain\Entries\Interfaces;

use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Models\Entry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface EntryRepositoryInterface
{
    public function newEntry(EntryData $entryData): Entry;

    public function updateEntry(int $id, EntryData $entryData): bool;

    public function getAllEntries(string $rangeMonthlyDate): Collection;

    public function getEntryById(int $id): Model;

    public function getAmountByEntryType(string $rangeMonthlyDate, string $amountType, string $entryType = null, string $exitType = null): Collection;
}
