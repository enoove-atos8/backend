<?php

namespace Domain\Entries\General\Interfaces;

use Domain\Entries\General\DataTransferObjects\EntryData;
use Domain\Entries\General\Models\Entry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface EntryRepositoryInterface
{
    public function newEntry(EntryData $entryData): Entry;

    public function updateEntry(int $id, EntryData $entryData): mixed;

    public function deleteEntry(int $id): bool;

    public function getAllEntries(string|null $rangeMonthlyDate): Collection;

    public function getAllEntriesByDateAndType(string $date, string $dateType = 'register' | 'transaction', string $entryType = '*'): Collection;

    public function getEntryById(int $id): Model | null;

    public function getAmountByEntryType(string $rangeMonthlyDate, string $amountType, string $entryType = null, string $exitType = null): Collection;
}
