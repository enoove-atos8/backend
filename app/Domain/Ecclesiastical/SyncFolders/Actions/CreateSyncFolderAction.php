<?php

namespace Domain\Ecclesiastical\SyncFolders\Actions;

use Domain\Ecclesiastical\SyncFolders\DataTransferObjects\SyncFoldersData;
use Domain\Ecclesiastical\SyncFolders\Interfaces\SyncFoldersRepositoryInterface;
use Domain\Ecclesiastical\SyncFolders\Models\SyncFolder;
use Infrastructure\Repositories\Ecclesiastical\SyncFolders\SyncFoldersRepository;

class CreateSyncFolderAction
{
    private SyncFoldersRepository $syncFoldersRepository;



    public function __construct(SyncFoldersRepositoryInterface $syncFoldersRepositoryInterface)
    {
        $this->syncFoldersRepository = $syncFoldersRepositoryInterface;
    }



    public function __invoke(SyncFoldersData $syncFoldersData)
    {
        // TODO: Implement __invoke() method.
    }
}
