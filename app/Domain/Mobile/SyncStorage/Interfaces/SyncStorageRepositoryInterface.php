<?php

namespace Domain\Mobile\SyncStorage\Interfaces;

use Application\Api\v1\Mobile\SyncStorage\Requests\ReceiptDataRequest;
use Domain\Mobile\SyncStorage\DataTransferObjects\SyncStorageData;
use Domain\Mobile\SyncStorage\Models\SyncStorage;

interface SyncStorageRepositoryInterface
{
    public function sendToDataServer(SyncStorageData $syncStorageData): SyncStorage;
}
