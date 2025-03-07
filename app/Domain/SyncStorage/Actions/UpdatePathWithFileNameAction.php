<?php

namespace Domain\SyncStorage\Actions;

use App\Domain\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;

class UpdatePathWithFileNameAction
{
    private SyncStorageRepository $syncStorageRepository;

    public function __construct(SyncStorageRepositoryInterface $syncStorageRepositoryInterface)
    {
        $this->syncStorageRepository = $syncStorageRepositoryInterface;
    }



    /**
     * @param int $id
     * @param string $fileName
     * @return mixed
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function execute(int $id, string $fileName): mixed
    {
        $syncStoragePathUpdated = $this->syncStorageRepository->updatePathWithFileName($id, $fileName);

        if($syncStoragePathUpdated)
            return $syncStoragePathUpdated;
        else
            throw new GeneralExceptions('', 500);
    }
}
