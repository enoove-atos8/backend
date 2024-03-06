<?php

namespace Domain\Entries\Consolidated\Actions;

use Domain\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Domain\Entries\Consolidated\Constants\ReturnMessages;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Entries\Consolidated\ConsolidatedEntriesRepository;
use Throwable;

class GetEntriesEvolutionConsolidatedAction
{
    private ConsolidatedEntriesRepository $consolidatedEntriesRepository;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepositoryInterface,
    )
    {
        $this->consolidatedEntriesRepository = $consolidatedEntriesRepositoryInterface;
    }

    /**
     * @param int $limit
     * @return Collection
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke(int $limit): Collection
    {
        $entriesConsolidation = $this->consolidatedEntriesRepository->getEntriesEvolutionConsolidation();
        $countMonthsNoConsolidated = $entriesConsolidation->where(ConsolidatedEntriesRepository::CONSOLIDATED_COLUMN,
                                                                BaseRepository::OPERATORS['EQUALS'],
                                                                0)->count();

        if ($entriesConsolidation->count() > 1 and ($entriesConsolidation->count() != $countMonthsNoConsolidated))
        {
            return $entriesConsolidation;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_REQUIRED_TWO_MONTHS_CONSOLIDATED, 404);
        }
    }
}
