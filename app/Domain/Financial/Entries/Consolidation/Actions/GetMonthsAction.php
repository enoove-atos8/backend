<?php

namespace Domain\Financial\Entries\Consolidation\Actions;

use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetMonthsAction
{

    private ConsolidationRepository $consolidationRepository;

    public function __construct(ConsolidatedEntriesRepositoryInterface $consolidationRepositoryInterface)
    {
        $this->consolidationRepository = $consolidationRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function execute(): Collection
    {
        $months = $this->consolidationRepository->getConsolidatedMonths();

        if(count($months) > 0)
        {
            return $months;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_GET_CONSOLIDATED_ENTRIES, 500);
        }
    }
}
