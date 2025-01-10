<?php

namespace Infrastructure\Repositories\Ecclesiastical\SyncFolders;

use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Domain\Ecclesiastical\Folders\Interfaces\SyncFoldersRepositoryInterface;
use Domain\Ecclesiastical\Folders\Models\SyncFolder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\BaseRepository;

class SyncFoldersRepository extends BaseRepository implements SyncFoldersRepositoryInterface
{
    protected mixed $model = SyncFolder::class;

    const TABLE_NAME = 'google_drive_ecclesiastical_groups_folders';
    const ENTRY_TYPE_COLUMN = 'entry_type';
    const ENTRIES_IN_CULT_VALUE = 'cult';
    const DEPOSIT_RECEIPTS_IN_CULT_VALUE = 'deposit';


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param bool $cultEntries
     * @param bool $depositReceipt
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getFolders(bool $cultEntries, bool $depositReceipt): Collection
    {
        $folders = null;

        if(!$cultEntries && !$depositReceipt)
            $folders =  $this->getItemsByWhere();

        else if($cultEntries && !$depositReceipt)
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
        else if(!$cultEntries && $depositReceipt)
        {
            $conditions = [
                [
                    'field' => self::ENTRY_TYPE_COLUMN,
                    'operator' => BaseRepository::OPERATORS['EQUALS'],
                    'value' => self::DEPOSIT_RECEIPTS_IN_CULT_VALUE
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
