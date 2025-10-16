<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use App\Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
use Illuminate\Support\Collection;

interface AccountFileRepositoryInterface
{
    /**
     * Save a new file
     *
     * @param AccountFileData $accountFileData
     * @return AccountFileData
     */
    public function saveFile(AccountFileData $accountFileData): AccountFileData;


    /**
     * Change file processing status
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function changeFileProcessingStatus(int $id, string $status): bool;


    /**
     * Get files by account id
     *
     * @param int $accountId
     * @return mixed
     */
    public function getFilesByAccountId(int $accountId): Collection;


    /**
     * Get files by id
     *
     * @param int $id
     * @return mixed
     */
    public function getFilesById(int $id): AccountFileData;


    /**
     * Delete a file from by account id and id file
     *
     * @return Collection
     */
    public function deleteFile(int $accountId, int $id): mixed;
}
