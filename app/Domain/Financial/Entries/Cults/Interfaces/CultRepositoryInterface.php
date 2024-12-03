<?php

namespace App\Domain\Financial\Entries\Cults\Interfaces;

use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Cults\DataTransferObjects\CultData;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface CultRepositoryInterface
{
    public function createCult(CultData $cultData): Model;

    public function getCults(): Collection;


}
