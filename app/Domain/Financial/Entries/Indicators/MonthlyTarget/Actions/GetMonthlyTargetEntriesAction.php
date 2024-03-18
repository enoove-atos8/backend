<?php

namespace Domain\Financial\Entries\Indicators\MonthlyTarget\Actions;

use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Domain\Financial\Entries\Indicators\MonthlyTarget\Interfaces\MonthlyTargetEntriesRepositoryInterface;
use Infrastructure\Repositories\Financial\Entries\MonthlyTarget\MonthlyTargetEntriesRepository;
use Throwable;

class GetMonthlyTargetEntriesAction
{
    private MonthlyTargetEntriesRepository $monthlyTargetEntriesRepository;

    public function __construct(
        MonthlyTargetEntriesRepositoryInterface $monthlyTargetEntriesRepositoryInterface
    )
    {
        $this->monthlyTargetEntriesRepository = $monthlyTargetEntriesRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): float|int
    {
        $monthlyTarget = 27000;
        $titheAmount = $this->monthlyTargetEntriesRepository->getHigherEntryAmount(EntryRepository::TITHE_VALUE);

        return (floatval($titheAmount->tithe_amount) / $monthlyTarget);
    }
}
