<?php

namespace App\Domain\Financial\Settings\Actions;

use App\Domain\Financial\Settings\Constants\ReturnMessages;
use App\Domain\Financial\Settings\Interfaces\FinancialSettingsRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Settings\FinancialSettingsRepository;
use Throwable;

class GetFinancialSettingsAction
{
    private FinancialSettingsRepository $financialSettingsRepository;

    public function __construct(
        FinancialSettingsRepositoryInterface $financialSettingsRepositoryInterface,
    )
    {
        $this->financialSettingsRepository = $financialSettingsRepositoryInterface;
    }


    /**
     * @throws Throwable
     */
    public function execute(string $budgetType = FinancialSettingsRepository::BUDGET_TYPE_TITHES): ?Model
    {
        $settingData = $this->financialSettingsRepository->getSettingsByType($budgetType);

        if ($settingData != null) {
            return $settingData;
        }

        return null;
    }
}
