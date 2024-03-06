<?php

namespace Domain\Entries\Consolidated\Actions;

use Domain\Entries\Consolidated\Constants\ReturnMessages;
use Domain\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use Domain\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Entries\Consolidated\ConsolidatedEntriesRepository;

class GetConsolidatedEntriesStatusByDateAction
{
    private ConsolidatedEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function __invoke(ConsolidationEntriesData $consolidationEntriesData): array
    {
        $consolidatedEntries = $this->consolidationEntriesRepository->getByDate($consolidationEntriesData->date);

        if($consolidatedEntries)
        {
            return [
                'message'   =>  ReturnMessages::ERROR_GET_CONSOLIDATED_ENTRIES,
                'data'      =>  [
                    'consolidatedEntries'    =>  $consolidatedEntries,
                ]
            ];
        }
        else
        {
            throw new GeneralExceptions('', 404);
        }
    }
}
