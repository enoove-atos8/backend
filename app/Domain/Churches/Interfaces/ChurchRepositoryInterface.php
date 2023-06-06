<?php

namespace Domain\Churches\Interfaces;

use Domain\Churches\DataTransferObjects\ChurchData;
use Domain\Churches\Models\Church;
use Illuminate\Support\Collection;
use Domain\Churches\Models\Tenant;
use Infrastructure\Repositories\Church\ChurchRepository;

interface ChurchRepositoryInterface
{
    public function newChurch(ChurchData $churchData): Church;
}
