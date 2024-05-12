<?php

namespace Domain\Financial\Entries\Indicators\AmountDevolutions\Actions;

use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use App\Infrastructure\Repositories\Financial\Entries\Indicators\AmountDevolutionEntries\AmountDevolutionEntriesRepository;
use Domain\Financial\Entries\Indicators\AmountDevolutions\Interfaces\AmountDevolutionRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

class GetEntriesDevolutionAmountAction
{
    private AmountDevolutionEntriesRepository $amountDevolutionEntriesRepository;

    public function __construct(AmountDevolutionRepositoryInterface $amountDevolutionRepositoryInterface)
    {
        $this->amountDevolutionEntriesRepository = $amountDevolutionRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     */
    public function __invoke(): array
    {
        $entriesDevolutions = $this->amountDevolutionEntriesRepository->getDevolutionEntriesAmount();
        $amount = $entriesDevolutions->sum(EntryRepository::AMOUNT_COLUMN);
        $totalEntries = $entriesDevolutions->count();

        return [
            'devolutionEntries' =>  [
                'indicators'    =>  [
                    'amount'        =>  $amount,
                    'totalEntries'  =>  $totalEntries,
                ]
            ]
        ];
    }
}
