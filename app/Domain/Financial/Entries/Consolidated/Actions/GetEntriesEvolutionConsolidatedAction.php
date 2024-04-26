<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;

use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Settings\Actions\GetFinancialSettingsAction;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class GetEntriesEvolutionConsolidatedAction
{
    private ConsolidationEntriesRepository $consolidatedEntriesRepository;
    private GetFinancialSettingsAction $getFinancialSettingsAction;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepositoryInterface,
        GetFinancialSettingsAction $getFinancialSettingsAction
    )
    {
        $this->consolidatedEntriesRepository = $consolidatedEntriesRepositoryInterface;
        $this->getFinancialSettingsAction = $getFinancialSettingsAction;
    }

    /**
     * @param string $consolidatedValues
     * @param int $limit
     * @return array
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function __invoke(string $consolidatedValues, int $limit = 6): array
    {
        $entriesConsolidation = $this->consolidatedEntriesRepository->getEntriesEvolutionConsolidation($consolidatedValues, $limit);
        $countMonthsNoConsolidated = $entriesConsolidation->where(ConsolidationEntriesRepository::CONSOLIDATED_COLUMN,
                                                                BaseRepository::OPERATORS['EQUALS'],
                                                                0)->count();

        //if (($entriesConsolidation->count() > 1 or $limit < 6) and ($entriesConsolidation->count() != $countMonthsNoConsolidated))
        if ($entriesConsolidation->count() > 1)
        {
            $financialSettings = $this->getFinancialSettingsAction->__invoke();
            $monthlyTarget = $financialSettings->monthly_budget_tithes;
            $maxTitheAmount = $entriesConsolidation->max(ConsolidationEntriesRepository::AMOUNT_TITHE_COLUMN);

            return [
                'amounts'    =>  [
                    'monthlyTarget' =>  (float) $monthlyTarget,
                    'maxAmount'     =>  (float) $maxTitheAmount,
                ],
                'collection'    =>  $entriesConsolidation
            ];
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_REQUIRED_TWO_MONTHS_CONSOLIDATED, 404);
        }
    }
}
