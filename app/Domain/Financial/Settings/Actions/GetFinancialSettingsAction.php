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
    public function __invoke(): Model
    {
        $settingData = $this->financialSettingsRepository->getCurrentFinancialSettingsData();

        if($settingData != null)
        {
            return $settingData;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::SETTINGS_INFO_NOT_FOUND, 404);
        }
    }
}
