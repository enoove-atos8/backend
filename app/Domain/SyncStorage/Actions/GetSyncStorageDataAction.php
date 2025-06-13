<?php

namespace Domain\SyncStorage\Actions;

use App\Domain\SyncStorage\DataTransferObjects\SyncStorageData;
use App\Domain\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;
use Throwable;

class GetSyncStorageDataAction
{
    private SyncStorageRepositoryInterface $syncStorageRepository;
    public function __construct(
        SyncStorageRepositoryInterface  $syncStorageRepositoryInterface,
    )
    {
        $this->syncStorageRepository = $syncStorageRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function execute(string $docType, ?string $docSubType = null, array|string|null $exceptDocSubType = null): Collection
    {
        return $this->syncStorageRepository->getSyncStorageData($docType, $docSubType, $exceptDocSubType);
    }
}
