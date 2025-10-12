<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Interfaces;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountFileData;
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
     * Save a new account or update an existing one.
     *
     * @param int $accountId
     * @return mixed
     */
    public function getFiles(int $accountId): Collection;


    /**
     * Delete a file from by account id and id file
     *
     * @return Collection
     */
    public function deleteFile(int $accountId, int $id): mixed;
}
