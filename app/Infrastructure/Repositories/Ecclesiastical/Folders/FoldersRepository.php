<?php

namespace Infrastructure\Repositories\Ecclesiastical\Folders;

use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Domain\Ecclesiastical\Folders\Interfaces\FoldersRepositoryInterface;
use Domain\Ecclesiastical\Folders\Models\Folder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\BaseRepository;

class FoldersRepository extends BaseRepository implements FoldersRepositoryInterface
{
    protected mixed $model = Folder::class;

    const TABLE_NAME = 'google_drive_ecclesiastical_groups_folders';


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @throws BindingResolutionException
     */
    public function getFolders(): Collection
    {
        return $this->getItemsByWhere();
    }
}
