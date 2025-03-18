<?php

namespace App\Domain\Financial\Exits\Indicators\Actions;

use Domain\Financial\Entries\Indicators\AmountDevolutions\Actions\GetEntriesDevolutionAmountAction;
use Domain\Financial\Entries\Indicators\AmountToCompensate\Actions\GetEntriesAmountToCompesateActions;
use Domain\Financial\Entries\Indicators\TithesBalance\Actions\GetTithesBalanceActions;
use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Actions\GetTithesMonthlyTargetEntriesAction;
use Domain\Financial\Entries\Indicators\TotalGeneral\Actions\GetTotalGeneralEntriesAction;
use Domain\Financial\Exits\Indicators\Amount\GetExitsAmountAction;
use Illuminate\Support\Collection;
use Throwable;

class HandleExitsIndicatorsAction
{
    const EXITS_AMOUNT_INDICATOR = 'exitsAmount';


    private GetExitsAmountAction $getExitsAmountAction;

    public function __construct(
        GetExitsAmountAction $getExitsAmountAction,
    )
    {
        $this->getExitsAmountAction = $getExitsAmountAction;
    }


    /**
     * @throws Throwable
     */
    public function execute(string $indicator, string|null $dates, array $filters): array
    {
        $exits = null;

        if($indicator == self::EXITS_AMOUNT_INDICATOR)
            $exits = $this->getExitsAmountAction->execute($dates, $filters);

        return [
            'indicators' =>  [
                'exitsAmount'    =>  [
                    'qtdExits'      =>  !is_null($exits) ? $exits['qtdExits'] : 0,
                    'amountExits'   =>  !is_null($exits) ? $exits['amountExits'] : 0,
                ]
            ]
        ];

    }
}
