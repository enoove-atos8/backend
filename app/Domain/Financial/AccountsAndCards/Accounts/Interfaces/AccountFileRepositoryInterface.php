<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Illuminate\Support\Collection;

interface AccountFileRepositoryInterface
{
    /**
     * Save a new file
     */
    public function saveFile(AccountFileData $accountFileData): AccountFileData;

    /**
     * Change file processing status
     */
    public function changeFileProcessingStatus(int $id, string $status): bool;

    /**
     * Get files by account id
     *
     * @return mixed
     */
    public function getFilesByAccountId(int $accountId): Collection;

    /**
     * Verify id exist file to account id and reference date
     *
     * @return mixed
     */
    public function existFileByReferenceDate(int $accountId, string $referenceDate): bool;

    /**
     * Get files by id
     *
     * @return mixed
     */
    public function getFilesById(int $id): AccountFileData;

    /**
     * Delete a file from by account id and id file
     *
     * @return Collection
     */
    public function deleteFile(int $accountId, int $id): mixed;

    /**
     * Get the last processed file for an account
     */
    public function getLastProcessedFile(int $accountId): ?AccountFileData;

    /**
     * Get file by account id and reference date
     */
    public function getFileByAccountAndReferenceDate(int $accountId, string $referenceDate): ?AccountFileData;

    /**
     * Get all processed files after a given reference date for an account
     *
     * @param  string  $referenceDate  Format: Y-m
     */
    public function getProcessedFilesAfterDate(int $accountId, string $referenceDate): Collection;

    /**
     * Reset file status to 'to_process' for reprocessing
     */
    public function resetFileStatusToProcess(int $fileId): bool;

    /**
     * Get the earliest (oldest) processed file for an account
     */
    public function getEarliestProcessedFile(int $accountId): ?AccountFileData;
}
