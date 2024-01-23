<?php

namespace Domain\ConsolidationEntries\Interfaces;

use Domain\ConsolidationEntries\DataTransferObjects\ConsolidationEntriesData;
use Domain\ConsolidationEntries\Models\ConsolidationEntries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ConsolidationEntriesRepositoryInterface
{
    public function getByDate(string $date): Model|null;

    public function getConsolidationEntriesByStatus(int $status): Collection;

    public function new(ConsolidationEntriesData $consolidationEntriesData): void;

    public function updateConsolidationStatus(array $dates, string $status): bool;
    public function deleteConsolidationEntry(string $date): bool;
}
