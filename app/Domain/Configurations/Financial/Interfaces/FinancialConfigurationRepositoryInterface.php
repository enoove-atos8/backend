<?php

namespace Domain\Configurations\Financial\Interfaces;

use Domain\Configurations\Financial\Models\FinancialConfigurations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface FinancialConfigurationRepositoryInterface
{
    /**
     * @return Collection
     */
    public function getFinancialConfigurationData(): Collection;
}
