<?php

namespace Domain\Entries\Consolidated\Actions;

use Domain\Configurations\Financial\Actions\GetFinancialConfigurationsAction;
use Domain\Entries\Consolidated\Constants\ReturnMessages;
use Domain\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Configurations\Financial\FinancialConfigurationsRepository;
use Infrastructure\Repositories\Entries\Consolidated\ConsolidatedEntriesRepository;

class GetMonthlyTitheEntryTargetAction
{
    private ConsolidatedEntriesRepository $consolidationEntriesRepository;
    private GetFinancialConfigurationsAction $getFinancialConfigurationsAction;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface,
        GetFinancialConfigurationsAction $getFinancialConfigurationsAction
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
        $this->getFinancialConfigurationsAction = $getFinancialConfigurationsAction;
    }


    /**
     */
    public function __invoke(string $consolidated, bool $returnNumberEntriesNoCompensate = false): Collection
    {

    }
}
