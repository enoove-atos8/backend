<?php

namespace App\Domain\AI\Search\Interfaces;

use App\Domain\AI\Search\DataTransferObjects\AiSearchHistoryData;
use Illuminate\Support\Collection;

interface AiSearchHistoryRepositoryInterface
{
    /**
     * Save a new search history record
     */
    public function save(AiSearchHistoryData $data): AiSearchHistoryData;

    /**
     * Get recent searches by user id
     */
    public function getRecentByUserId(int $userId, int $limit = 10): Collection;

    /**
     * Get all successful searches by user id
     */
    public function getSuccessfulByUserId(int $userId, int $limit = 50): Collection;

    /**
     * Execute a validated SQL query and return results as array
     *
     * @return array<int, array<string, mixed>>
     */
    public function executeQuery(string $sql): array;

    /**
     * Get the last successful search by user id
     */
    public function getLastSuccessfulByUserId(int $userId): ?AiSearchHistoryData;
}
