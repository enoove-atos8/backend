<?php

namespace App\Domain\Financial\Entries\Consolidated\Interfaces;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ConsolidatedEntriesRepositoryInterface
{
    public function getByDate(string $date): Model|null;

    public function getConsolidatedEntriesByStatus(string $consolidated = 'all', int $limit = 6, string $orderDirection = 'DESC'): Collection;

    public function getEntriesEvolutionConsolidation(string $consolidatedValues, int $limit = 6): Collection;

    public function new(ConsolidationEntriesData $consolidationEntriesData): void;

    public function updateConsolidationStatus(array $dates, string $status): bool;

    public function updateTotalValueConsolidation(string $date, string $amount, string $column): bool;

    public function deleteConsolidationEntry(string $date): bool;
}
