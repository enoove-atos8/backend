<?php

namespace Domain\Ecclesiastical\Folders\Interfaces;

use Domain\Ecclesiastical\Divisions\Models\Division;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseFoldersRepositoryInterface
{
    /**
     * @return Collection
     */
    public function getBaseFolders(): Collection;
}
