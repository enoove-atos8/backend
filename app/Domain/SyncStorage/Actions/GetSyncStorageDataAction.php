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
    private SyncStorageRepository $syncStorageRepository;
    public function __construct(
        SyncStorageRepositoryInterface  $syncStorageRepositoryInterface,
    )
    {
        $this->syncStorageRepository = $syncStorageRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function execute(string $docType): Collection
    {
        return $this->syncStorageRepository
            ->getSyncStorageData($docType)
            ->map(function ($item) {
                $formattedItem = collect($item)->mapWithKeys(function ($value, $key) {
                    return [Str::camel($key) => $value];
                })->all();

                return new SyncStorageData($formattedItem);
            });
    }
}
