<?php

namespace App\Domain\Financial\Entries\Consolidation\Actions;

use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;

class GetConsolidatedEntriesStatusByDateAction
{
    private ConsolidationRepository $consolidationEntriesRepository;

    public function __construct(
        ConsolidatedEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function execute(ConsolidationEntriesData $consolidationEntriesData): array
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
