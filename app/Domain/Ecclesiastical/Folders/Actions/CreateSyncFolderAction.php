<?php

namespace Domain\Ecclesiastical\Folders\Actions;

use Domain\Ecclesiastical\Folders\DataTransferObjects\SyncFoldersData;
use Domain\Ecclesiastical\Folders\Interfaces\SyncFoldersRepositoryInterface;
use Domain\Ecclesiastical\Folders\Models\SyncFolder;
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
        // TODO:
    }
}
