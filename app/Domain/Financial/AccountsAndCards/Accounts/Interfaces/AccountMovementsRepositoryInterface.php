<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountMovementsData;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface AccountMovementsRepositoryInterface
{
    public function createMovement(AccountMovementsData $accountMovementsData): mixed;

    /**
     * Create multiple movements in bulk
     *
     * @param  Collection  $movements  Collection of ExtractorFileData
     */
    public function bulkCreateMovements(Collection $movements, int $accountId, int $fileId): bool;

    /**
     * Delete movements by account and file
     */
    public function deleteByAccountAndFile(int $accountId, int $fileId): bool;

    /**
     * Delete movements by account and reference date (month)
     *
     * @param  string  $referenceDate  Format: Y-m
     */
    public function deleteByAccountAndReferenceDate(int $accountId, string $referenceDate): bool;

    /**
     * Get movements by account id and reference date
     */
    public function getMovements(int $accountId, string $referenceDate, bool $paginate = true): Collection|Paginator;

    /**
     * Get movements by account and file
     */
    public function getMovementsByAccountAndFile(int $accountId, int $fileId): Collection;

    /**
     * Bulk update conciliation status
     */
    public function bulkUpdateConciliationStatus(array $reconciliationMap): void;
}
