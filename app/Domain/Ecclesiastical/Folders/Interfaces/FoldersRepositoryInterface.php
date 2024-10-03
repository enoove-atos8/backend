<?php

namespace Domain\Ecclesiastical\Folders\Interfaces;

use Domain\Ecclesiastical\Divisions\Models\Division;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface FoldersRepositoryInterface
{
    /**
     * @param bool $cultEntries
     * @param bool $depositReceipt
     * @return Collection
     */
    public function getFolders(bool $cultEntries, bool $depositReceipt): Collection;
}
