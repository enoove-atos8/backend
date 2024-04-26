<?php

namespace Domain\Financial\Entries\Indicators\AmountToCompensate\Actions;

use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use Domain\Financial\Entries\Indicators\AmountToCompensate\Interfaces\AmountToCompensateRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\Financial\Entries\Indicators\AmountToCompensate\AmountToCompensateRepository;

class GetEntriesAmountToCompesateActions
{
    private AmountToCompensateRepository $amountToCompensateRepository;

    public function __construct(AmountToCompensateRepositoryInterface $amountToCompensateRepositoryInterface)
    {
        $this->amountToCompensateRepository = $amountToCompensateRepositoryInterface;
    }



    /**
     * @throws BindingResolutionException
     */
    public function __invoke(): array
    {
        $entriesToCompensate = $this->amountToCompensateRepository->getEntriesAmountToCompensate();
        $amount = $entriesToCompensate->sum(EntryRepository::AMOUNT_COLUMN);
        $totalEntries = $entriesToCompensate->count();

        return [
            'toCompensateEntries' =>  [
                'indicators'    =>  [
                    'amount'        =>  $amount,
                    'totalEntries'  =>  $totalEntries,
                ]
            ]
        ];
    }
}
