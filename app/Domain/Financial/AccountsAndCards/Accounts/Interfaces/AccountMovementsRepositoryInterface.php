<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountMovementsData;
use Illuminate\Support\Collection;

interface AccountMovementsRepositoryInterface
{
    public function createMovement(AccountMovementsData $accountMovementsData): mixed;

    /**
     * Create multiple movements in bulk
     *
     * @param Collection $movements Collection of ExtractorFileData
     * @param int $accountId
     * @param int $fileId
     * @return bool
     */
    public function bulkCreateMovements(Collection $movements, int $accountId, int $fileId): bool;

    /**
     * Delete movements by account and file
     *
     * @param int $accountId
     * @param int $fileId
     * @return bool
     */
    public function deleteByAccountAndFile(int $accountId, int $fileId): bool;
}
