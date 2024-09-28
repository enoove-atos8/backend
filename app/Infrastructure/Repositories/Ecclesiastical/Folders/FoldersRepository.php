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
    const ENTRY_TYPE_COLUMN = 'entry_type';
    const ENTRIES_IN_CULT_VALUE = 'entries_in_cult';


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param bool $cashTithes
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getFolders(bool $cashTithes): Collection
    {
        if(!$cashTithes)
            $folders =  $this->getItemsByWhere();
        else
        {
            $conditions = [
                [
                    'field' => self::ENTRY_TYPE_COLUMN,
                    'operator' => BaseRepository::OPERATORS['EQUALS'],
                    'value' => self::ENTRIES_IN_CULT_VALUE
                ]
            ];

            $folders =  $this->getItemsByWhere(
                ['*'],
                $conditions
            );
        }

        return $folders;
    }
}
