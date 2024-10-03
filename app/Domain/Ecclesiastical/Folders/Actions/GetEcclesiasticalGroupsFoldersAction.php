<?php

namespace Domain\Ecclesiastical\Folders\Actions;

use Domain\Ecclesiastical\Folders\Interfaces\FoldersRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Ecclesiastical\Folders\FoldersRepository;
use Throwable;

class GetEcclesiasticalGroupsFoldersAction
{
    private FoldersRepository $foldersRepository;
    public function __construct(
        FoldersRepositoryInterface  $foldersRepositoryInterface,
    )
    {
        $this->foldersRepository = $foldersRepositoryInterface;
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
