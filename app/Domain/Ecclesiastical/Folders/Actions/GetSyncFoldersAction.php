<?php

namespace Domain\Ecclesiastical\Folders\Actions;

use Domain\Ecclesiastical\Folders\Interfaces\SyncFoldersRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Ecclesiastical\SyncFolders\SyncFoldersRepository;
use Throwable;

class GetSyncFoldersAction
{
    private SyncFoldersRepository $foldersRepository;
    public function __construct(
        SyncFoldersRepositoryInterface  $syncFoldersRepository,
    )
    {
        $this->foldersRepository = $syncFoldersRepository;
    }



    /**
     * @throws Throwable
     */
    public function __invoke(): Collection | null
    {
        $folders = $this->foldersRepository->getFolders();

        if ($folders->isNotEmpty())
            return $folders;

        return null;
    }
}
