<?php

namespace App\Domain\Financial\Settings\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface FinancialSettingsRepositoryInterface
{
    /**
     * @return Model|null
     */
    public function getCurrentFinancialSettingsData(): Model | null;
}
