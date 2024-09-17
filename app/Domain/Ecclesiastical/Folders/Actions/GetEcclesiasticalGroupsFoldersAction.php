<?php

namespace Domain\Ecclesiastical\Folders\Actions;

use Domain\Ecclesiastical\Folders\Interfaces\FoldersRepositoryInterface;
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
    public function __invoke(): Collection | null
    {
        $folders = $this->foldersRepository->getFolders();

        if(count($folders) > 0)
        {
            return $folders;
        }
        else
        {
            return null;
        }
    }
}
