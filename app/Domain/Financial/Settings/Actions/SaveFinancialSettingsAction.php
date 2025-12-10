<?php

namespace App\Domain\Financial\Settings\Actions;

use App\Domain\Financial\Settings\DataTransferObjects\FinancialSettingsData;
use App\Domain\Financial\Settings\Interfaces\FinancialSettingsRepositoryInterface;

class SaveFinancialSettingsAction
{
    public function __construct(
        private FinancialSettingsRepositoryInterface $financialSettingsRepository
    ) {}

    public function execute(FinancialSettingsData $data): bool
    {
        return $this->financialSettingsRepository->saveSettings($data);
    }
}
