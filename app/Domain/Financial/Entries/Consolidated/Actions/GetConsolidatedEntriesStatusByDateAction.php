<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;

use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;

class GetConsolidatedEntriesStatusByDateAction
{
    private ConsolidationEntriesRepository $consolidationEntriesRepository;

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
