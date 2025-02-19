<?php

namespace Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Actions;

use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Domain\Financial\Settings\Actions\GetFinancialSettingsAction;
use App\Infrastructure\Repositories\Financial\Entries\Indicators\TithesMonthlyTarget\TithesMonthlyTargetEntriesRepository;
use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Interfaces\TithesMonthlyTargetEntriesRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class GetTithesMonthlyTargetEntriesAction
{
    private TithesMonthlyTargetEntriesRepository $tithesMonthlyTargetEntriesRepository;
    private GetFinancialSettingsAction $getFinancialSettingsAction;

    public function __construct(
        TithesMonthlyTargetEntriesRepositoryInterface $tithesMonthlyTargetEntriesRepositoryInterface,
        GetFinancialSettingsAction $getFinancialSettingsAction
    )
    {
        $this->tithesMonthlyTargetEntriesRepository = $tithesMonthlyTargetEntriesRepositoryInterface;
        $this->getFinancialSettingsAction = $getFinancialSettingsAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(): array
    {
        $limit = 3;
        $monthlyValues = [];
        $monthlyDates = [];
        $lastConsolidatedTitheEntries = $this->tithesMonthlyTargetEntriesRepository->getLastConsolidatedTitheEntries($limit);

        if($lastConsolidatedTitheEntries->count() > 1)
        {
            $financialSettings = $this->getFinancialSettingsAction->execute();
            $monthlyTarget = $financialSettings->monthly_budget_tithes;
            $lastEntryConsolidated = $lastConsolidatedTitheEntries[0];
            $monthlyTargetPercent = floatval($lastEntryConsolidated->tithe_amount) / $monthlyTarget;
            $lastDate = $lastEntryConsolidated->date;

            foreach ($lastConsolidatedTitheEntries->reverse() as $value)
            {
                $monthlyValues [] = $value->tithe_amount;
                $monthlyDates [] = $value->date;
            }

            return [
                    'monthlyTarget' =>  [
                        'indicators'    =>  [
                            'target'    =>  $monthlyTargetPercent,
                            'date'      =>  $lastDate,
                            'variation' =>  ''
                        ],
                        'chart' =>  [
                            'data'  =>  [
                                'labels'    =>  $monthlyDates,
                                'series'    =>  [
                                    'name'  =>  'Alvo mensal',
                                    'data'  =>  $monthlyValues
                                ]
                            ]
                        ]
                    ]
                ];
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_REQUIRED_TWO_MONTHS_CONSOLIDATED, 404);
        }
    }
}
