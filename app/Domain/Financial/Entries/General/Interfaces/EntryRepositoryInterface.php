<?php

namespace App\Domain\Financial\Entries\General\Interfaces;

use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\General\Models\Entry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface EntryRepositoryInterface
{
    public function newEntry(EntryData $entryData): Entry;

    public function updateEntry(int $id, EntryData $entryData): mixed;
    public function updateIdentificationPending(int $entryId, int $identificationPending): mixed;
    public function updateTimestampValueCpf(int $entryId, string $timestampValueCpf): mixed;
    public function updateReceiptLink(int $entryId, string $receiptLink): mixed;

    public function deleteEntry(int $id): bool;

    public function getAllEntriesWithMembersAndReviewers(string|null $rangeMonthlyDate, string $transactionCompensation = 'to_compensate' | 'compensated' | '*', array $filters = [], array $orderBy = []): Collection | Paginator;

    public function getAllEntries(string|null $rangeMonthlyDate): Collection;

    public function getDevolutionEntries(string|null $rangeMonthlyDate, bool $devolutionStatus, array $orderBy): Collection | Paginator;

    public function getAllEntriesByDateAndType(string $date, string $dateType = 'register' | 'transaction', string $entryType = '*'): Collection;

    public function getEntryById(int $id): Model | null;
    public function getEntryByTimestampValueCpf(string $timestampValueCpf): Model | null;

    public function getAmountByEntryType(string $rangeMonthlyDate, string $entryType = '*'): mixed;

    public function applyFilters(array $filters, bool $joinQuery, bool $returnConditions);
}
