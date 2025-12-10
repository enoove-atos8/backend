<?php

namespace App\Domain\Financial\Settings\Interfaces;

use App\Domain\Financial\Settings\DataTransferObjects\FinancialSettingsData;
use Illuminate\Database\Eloquent\Model;

interface FinancialSettingsRepositoryInterface
{
    /**
     * @param string $budgetType
     * @return Model|null
     */
    public function getSettingsByType(string $budgetType): Model|null;

    /**
     * @param FinancialSettingsData $data
     * @return bool
     */
    public function saveSettings(FinancialSettingsData $data): bool;
}
