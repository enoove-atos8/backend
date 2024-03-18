<?php

namespace Domain\Financial\Entries\Indicators\Actions;

use Domain\Financial\Entries\Indicators\MonthlyTarget\Actions\GetMonthlyTargetEntriesAction;
use Illuminate\Support\Collection;
use Throwable;

class HandleEntriesIndicatorsAction
{
    const MONTHLY_TARGET_INDICATOR = 'monthlyTargetEntries';

    private GetMonthlyTargetEntriesAction $getMonthlyTargetEntriesAction;

    public function __construct(
        GetMonthlyTargetEntriesAction $getMonthlyTargetEntriesAction
    )
    {
        $this->getMonthlyTargetEntriesAction = $getMonthlyTargetEntriesAction;
    }


    /**
     * @throws Throwable
     */
    public function __invoke(string $indicator): float|int
    {
        if($indicator == self::MONTHLY_TARGET_INDICATOR)
            return $this->getMonthlyTargetEntriesAction->__invoke();
    }
}
