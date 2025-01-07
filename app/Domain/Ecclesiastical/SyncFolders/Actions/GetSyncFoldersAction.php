<?php

namespace Domain\Ecclesiastical\SyncFolders\Actions;

use Domain\Ecclesiastical\SyncFolders\Interfaces\SyncFoldersRepositoryInterface;
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
    public function __invoke(bool $cultEntries = false, bool $depositReceipt = false): Collection | null
    {
        $folders = $this->foldersRepository->getFolders($cultEntries, $depositReceipt);

        if ($folders->isNotEmpty())
            return $folders;

        return null;
    }
}
