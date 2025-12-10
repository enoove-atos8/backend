<?php

namespace Domain\Financial\Entries\Indicators\TithesBalance\Actions;

use App\Domain\Financial\Entries\Consolidation\Actions\GetConsolidatedEntriesByStatusAction;
use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Settings\Actions\GetFinancialSettingsAction;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
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
    public function execute(): array
    {
        $financialSettings = $this->getFinancialSettingsAction->execute();
        $monthlyTarget = $financialSettings?->budget_value ?? 0;
        $entriesConsolidated = $this->getConsolidatedEntriesByStatusAction->execute(1, false, 1, true);

        if($entriesConsolidated->count() > 0)
        {
            $totalEntriesTithes = (float) $entriesConsolidated[0]->tithe_amount;
            $date = $entriesConsolidated[0]->date;

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
