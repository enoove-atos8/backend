<?php

namespace App\Domain\Financial\Entries\Consolidation\Interfaces;

use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ConsolidatedEntriesRepositoryInterface
{
    public function getByDate(string $date): Model|null;

    public function getConsolidatedMonths(string | int $consolidated): Collection;

    public function getEntriesEvolutionConsolidation(string $consolidatedValues, int $limit = 6): Collection;

    public function new(ConsolidationEntriesData $consolidationEntriesData): void;

    public function updateConsolidationStatus(string $date, string $status): bool;

    public function checkConsolidationStatus(string $date): Model|null;

    public function updateTotalValueConsolidation(string $date, string $amount, string $column): bool;

    public function deleteConsolidationEntry(string $date): bool;
    
    public function reopenConsolidatedMonth(string $month): bool;
}
