<?php

namespace Domain\SyncStorage\Actions;

use App\Domain\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;

class UpdateStatusAction
{
    private SyncStorageRepository $syncStorageRepository;

    public function __construct(SyncStorageRepositoryInterface $syncStorageRepositoryInterface)
    {
        $this->syncStorageRepository = $syncStorageRepositoryInterface;
    }


    /**
     * @param int $syncStorageId
     * @param string $status
     * @return mixed
     * @throws BindingResolutionException
     */
    public function execute(int $syncStorageId, string $status): mixed
    {
        return $this->syncStorageRepository->updateStatus($syncStorageId, $status);
    }
}
