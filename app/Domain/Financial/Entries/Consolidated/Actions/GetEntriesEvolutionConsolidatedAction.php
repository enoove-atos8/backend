<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;

use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;

class GetEntriesEvolutionConsolidatedAction
{
    private ConsolidationEntriesRepository $consolidatedEntriesRepository;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidatedEntriesRepositoryInterface,
    )
    {
        $this->consolidatedEntriesRepository = $consolidatedEntriesRepositoryInterface;
    }

    /**
     * @param string $consolidatedValues
     * @param int $limit
     * @return Collection
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke(string $consolidatedValues, int $limit = 6): Collection
    {
        $entriesConsolidation = $this->consolidatedEntriesRepository->getEntriesEvolutionConsolidation($consolidatedValues, $limit);
        $countMonthsNoConsolidated = $entriesConsolidation->where(ConsolidationEntriesRepository::CONSOLIDATED_COLUMN,
                                                                BaseRepository::OPERATORS['EQUALS'],
                                                                0)->count();

        if (($entriesConsolidation->count() > 1 or $limit < 6) and ($entriesConsolidation->count() != $countMonthsNoConsolidated))
        {
            return $entriesConsolidation;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_REQUIRED_TWO_MONTHS_CONSOLIDATED, 404);
        }
    }
}
