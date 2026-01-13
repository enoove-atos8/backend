<?php

namespace App\Domain\Financial\Entries\Entries\Interfaces;

use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Entries\Models\Entry;
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

    public function setDuplicityAnalysis(int $entryId): void;

    public function deleteEntry(int $id): bool;

    public function getAllEntriesWithMembersAndReviewers(?string $dates, string $transactionCompensation, array $filters, array $orderBy): Collection|Paginator;

    public function getAllEntries(?string $dates): Collection;

    public function getDuplicitiesEntries(string $date): Collection;

    public function getDevolutionEntries(?string $dates, bool $devolutionStatus, array $orderBy): Collection|Paginator;

    public function getAllEntriesByDateAndType(string $date, string $dateType = 'register' | 'transaction', string $entryType = '*'): Collection;

    public function getEntryById(int $id): ?Model;

    public function getEntriesByCultId(int $id): ?Collection;

    public function getEntryByTimestampValueCpf(string $timestampValueCpf): ?Model;

    public function getAmountByEntryType(string $dates, string $entryType = '*'): mixed;

    public function deleteAnonymousEntriesByAccountAndDate(int $accountId, string $referenceDate): bool;

    public function bulkUpdateAccountId(array $entryIds, int $accountId): bool;

    public function getHistoryTitheByMemberId(int $memberId, int $months = 6): array;

    public function getTithesByMemberIds(array $memberIds): Collection;

    public function applyFilters(array $filters, bool $joinQuery, bool $returnConditions);
}
