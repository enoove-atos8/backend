<?php

namespace Domain\Financial\Entries\Indicators\Actions;

use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Actions\GetTithesMonthlyTargetEntriesAction;
use Illuminate\Support\Collection;
use Throwable;

class HandleEntriesIndicatorsAction
{
    const TITHES_MONTHLY_TARGET_INDICATOR = 'monthlyTargetEntries';

    private GetTithesMonthlyTargetEntriesAction $getTithesMonthlyTargetEntriesAction;

    public function __construct(
        GetTithesMonthlyTargetEntriesAction $getTithesMonthlyTargetEntriesAction
    )
    {
        $this->getTithesMonthlyTargetEntriesAction = $getTithesMonthlyTargetEntriesAction;
    }


    /**
     * @throws Throwable
     */
    public function __invoke(string $indicator): array
    {
        if($indicator == self::TITHES_MONTHLY_TARGET_INDICATOR)
            return $this->getTithesMonthlyTargetEntriesAction->__invoke();
    }
}
