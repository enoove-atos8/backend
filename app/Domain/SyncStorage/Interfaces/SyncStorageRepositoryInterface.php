<?php

namespace App\Domain\SyncStorage\Interfaces;

use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Domain\SyncStorage\Models\SyncStorage;
use Illuminate\Support\Collection;

interface SyncStorageRepositoryInterface
{
    /**
     * @param SyncStorageData $syncStorageData
     * @return SyncStorage
     */
    public function sendToDataServer(SyncStorageData $syncStorageData): SyncStorage;


    /**
     * @param string $docType
     * @return Collection
     */
    public function getSyncStorageData(string $docType): Collection;



    public function updatePathWithFileName(int $id, string $fileName): mixed;

    public function updateStatus(int $id, string $status): mixed;
}
