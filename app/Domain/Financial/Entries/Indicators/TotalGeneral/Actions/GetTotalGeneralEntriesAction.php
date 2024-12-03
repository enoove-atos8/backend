<?php

namespace Domain\Financial\Entries\Indicators\TotalGeneral\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\Entries\Indicators\TotalGeneral\Interfaces\TotalGeneralRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\Financial\Entries\Indicators\TotalGeneral\TotalGeneralRepository;

class GetTotalGeneralEntriesAction
{
    private TotalGeneralRepository $totalGeneralRepository;


    public function __construct(TotalGeneralRepositoryInterface $totalGeneralRepositoryInterface)
    {
        $this->totalGeneralRepository = $totalGeneralRepositoryInterface;
    }


    /**
     * @param string|null $dates
     * @param array $filters
     * @return array
     * @throws BindingResolutionException
     */
    public function __invoke(string|null $dates, array $filters): array
    {
        $entries = $this->totalGeneralRepository->getTotalGeneralEntries($dates, $filters);

        return [
            'qtdEntries'    =>  $entries->count(),
            'amountEntries'  =>  $entries->sum(EntryRepository::ENTRIES_AMOUNT_COLUMN_ALIAS),
        ];
    }

}
