<?php

namespace App\Domain\Financial\Entries\Cults\Interfaces;

use App\Domain\Financial\Entries\Cults\DataTransferObjects\CultData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface CultRepositoryInterface
{
    public function createCult(CultData $cultData): Model;

    public function updateCult($id, CultData $cultData): mixed;

    public function getCults(bool $paginate = true): Collection | Paginator;

    public function getCultById(int $id): Model;


}
