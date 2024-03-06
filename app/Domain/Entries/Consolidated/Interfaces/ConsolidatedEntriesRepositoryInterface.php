<?php

namespace Domain\Entries\Consolidated\Interfaces;

use Domain\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ConsolidatedEntriesRepositoryInterface
{
    public function getByDate(string $date): Model|null;

    public function getConsolidatedEntriesByStatus(string $consolidated = 'all'): Collection;

    public function getEntriesEvolutionConsolidation(string $consolidatedValues = '*', int $limit = 6): Collection;

    public function new(ConsolidationEntriesData $consolidationEntriesData): void;

    public function updateConsolidationStatus(array $dates, string $status): bool;

    public function updateTotalValueConsolidation(string $date, string $amount, string $column): bool;

    public function deleteConsolidationEntry(string $date): bool;
}
