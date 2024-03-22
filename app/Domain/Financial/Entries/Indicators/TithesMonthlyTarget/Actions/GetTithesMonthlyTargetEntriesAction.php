<?php

namespace Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Actions;

use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Interfaces\TithesMonthlyTargetEntriesRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

use Infrastructure\Repositories\Financial\Entries\TithesMonthlyTarget\TithesMonthlyTargetEntriesRepository;
use Throwable;
use function Webmozart\Assert\Tests\StaticAnalysis\length;

class GetTithesMonthlyTargetEntriesAction
{
    private TithesMonthlyTargetEntriesRepository $tithesMonthlyTargetEntriesRepository;

    public function __construct(
        TithesMonthlyTargetEntriesRepositoryInterface $tithesMonthlyTargetEntriesRepositoryInterface
    )
    {
        $this->tithesMonthlyTargetEntriesRepository = $tithesMonthlyTargetEntriesRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): array
    {
        $monthlyTarget = 27000;
        $limit = 3;
        $monthlyValues = [];
        $monthlyDates = [];
        $lastConsolidatedTitheEntries = $this->tithesMonthlyTargetEntriesRepository->getLastConsolidatedTitheEntries($limit);

        if($lastConsolidatedTitheEntries->count() > 0)
        {
            $lastEntryConsolidated = $lastConsolidatedTitheEntries[0];
            $monthlyTarget = floatval($lastEntryConsolidated->tithe_amount) / $monthlyTarget;
            $lastDate = $lastEntryConsolidated->date;

            foreach ($lastConsolidatedTitheEntries as $value)
            {
                $monthlyValues [] = $value->tithe_amount;
                $monthlyDates [] = $value->date;
            }

            return [
                    'indicators'    =>  [
                        'target'    =>  $monthlyTarget,
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
                ];
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_REQUIRED_TWO_MONTHS_CONSOLIDATED, 404);
        }
    }
}
