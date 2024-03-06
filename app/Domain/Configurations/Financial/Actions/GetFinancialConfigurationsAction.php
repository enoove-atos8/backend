<?php

namespace Domain\Configurations\Financial\Actions;

use Domain\Configurations\Financial\Interfaces\FinancialConfigurationRepositoryInterface;
use Domain\Configurations\Financial\Models\FinancialConfigurations;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Configurations\Financial\FinancialConfigurationsRepository;
use Throwable;

class GetFinancialConfigurationsAction
{
    private FinancialConfigurationsRepository $financialConfigurationsRepository;

    public function __construct(
        FinancialConfigurationRepositoryInterface $financialConfigurationsRepositoryInterface,
    )
    {
        $this->financialConfigurationsRepository = $financialConfigurationsRepositoryInterface;
    }


    /**
     * @throws Throwable
     */
    public function __invoke(): Collection
    {
        $configurations = $this->financialConfigurationsRepository->getFinancialConfigurationData();
        return  $configurations;
    }
}
