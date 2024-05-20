<?php

namespace Domain\Financial\Entries\Indicators\Actions;

use Domain\Financial\Entries\Indicators\AmountDevolutions\Actions\GetEntriesDevolutionAmountAction;
use Domain\Financial\Entries\Indicators\AmountToCompensate\Actions\GetEntriesAmountToCompesateActions;
use Domain\Financial\Entries\Indicators\TithesBalance\Actions\GetTithesBalanceActions;
use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Actions\GetTithesMonthlyTargetEntriesAction;
use Illuminate\Support\Collection;
use Throwable;

class HandleEntriesIndicatorsAction
{
    const TITHES_MONTHLY_TARGET_INDICATOR = 'monthlyTargetEntries';
    const TITHES_BALANCE_INDICATOR = 'tithesBalance';
    const NO_COMPENSATE_ENTRIES_INDICATOR = 'noCompensateEntries';
    const DEVOLUTION_ENTRIES_INDICATOR = 'devolutionEntries';
    const TOTAL_GENERAL_INDICATOR = 'totalGeneral';


    private GetTithesMonthlyTargetEntriesAction $getTithesMonthlyTargetEntriesAction;
    private GetTithesBalanceActions $getTithesBalanceActions;
    private GetEntriesAmountToCompesateActions $getEntriesAmountToCompesateActions;
    private GetEntriesDevolutionAmountAction $getEntriesDevolutionAmountAction;

    public function __construct(
        GetTithesMonthlyTargetEntriesAction $getTithesMonthlyTargetEntriesAction,
        GetTithesBalanceActions $getTithesBalanceActions,
        GetEntriesAmountToCompesateActions $getEntriesAmountToCompesateActions,
        GetEntriesDevolutionAmountAction $getEntriesDevolutionAmountAction
    )
    {
        $this->getTithesMonthlyTargetEntriesAction = $getTithesMonthlyTargetEntriesAction;
        $this->getTithesBalanceActions = $getTithesBalanceActions;
        $this->getEntriesAmountToCompesateActions = $getEntriesAmountToCompesateActions;
        $this->getEntriesDevolutionAmountAction = $getEntriesDevolutionAmountAction;
    }


    /**
     * @throws Throwable
     */
    public function __invoke(string $indicator, array $date): array
    {
        if($indicator == self::TITHES_MONTHLY_TARGET_INDICATOR)
            return $this->getTithesMonthlyTargetEntriesAction->__invoke();

        if($indicator == self::TITHES_BALANCE_INDICATOR)
            return $this->getTithesBalanceActions->__invoke();

        if($indicator == self::NO_COMPENSATE_ENTRIES_INDICATOR)
            return $this->getEntriesAmountToCompesateActions->__invoke();

        if($indicator == self::DEVOLUTION_ENTRIES_INDICATOR)
            return $this->getEntriesDevolutionAmountAction->__invoke();

        if($indicator == self::TOTAL_GENERAL_INDICATOR)
        {
            //return $this->getEntriesDevolutionAmountAction->__invoke();
            return [
                'totalGeneral' =>  [
                    'indicators'    =>  [
                        'qtdEntries'    =>  321,
                        'amount'        =>  123.23,
                    ]
                ]
            ];
        }

    }
}
