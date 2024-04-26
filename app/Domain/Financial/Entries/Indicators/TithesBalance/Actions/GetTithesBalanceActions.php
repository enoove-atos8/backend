<?php

namespace Domain\Financial\Entries\Indicators\TithesBalance\Actions;

use App\Domain\Financial\Entries\Consolidated\Actions\GetConsolidatedEntriesByStatusAction;
use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Settings\Actions\GetFinancialSettingsAction;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class GetTithesBalanceActions
{
    private GetConsolidatedEntriesByStatusAction $getConsolidatedEntriesByStatusAction;
    private GetFinancialSettingsAction $getFinancialSettingsAction;

    public function __construct(
        GetConsolidatedEntriesByStatusAction $getConsolidatedEntriesByStatusAction,
        GetFinancialSettingsAction $getFinancialSettingsAction)
    {
        $this->getConsolidatedEntriesByStatusAction = $getConsolidatedEntriesByStatusAction;
        $this->getFinancialSettingsAction = $getFinancialSettingsAction;
    }


    /**
     * @throws GeneralExceptions|Throwable
     */
    public function __invoke(): array
    {
        $financialSettings = $this->getFinancialSettingsAction->__invoke();
        $monthlyTarget = $financialSettings->monthly_budget_tithes;
        $entriesConsolidated = $this->getConsolidatedEntriesByStatusAction->__invoke(1, false, 1, true);

        if($entriesConsolidated->count() > 0)
        {
            $totalEntriesTithes = null;
            $date = null;

            foreach ($entriesConsolidated as $value){
                $totalEntriesTithes = (float) $value->tithe_amount;
                $date = $value->date;
            }

            $expectedTotalTithes = (float) $monthlyTarget;
            $balance = $totalEntriesTithes - $expectedTotalTithes;
            $percentTarget = $totalEntriesTithes / $expectedTotalTithes;

            return [
                'tithesBalance' =>  [
                    'indicators'    =>  [
                        'totalEntries'    =>  $totalEntriesTithes,
                        'expectedTotal'   =>  $expectedTotalTithes,
                        'date'            =>  $date,
                        'percentTarget'   =>  $percentTarget,
                        'balanceTotal'    =>  $balance
                    ]
                ]
            ];
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_GET_CONSOLIDATED_ENTRIES, 404);
        }

    }
}
