<?php

namespace App\Domain\Financial\Entries\Consolidated\Actions;

use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidated\DataTransferObjects\MonthlyTargetEntriesData;
use App\Domain\Financial\Entries\Consolidated\Interfaces\MonthlyTargetEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\MonthlyTargetEntriesRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;

class GetConsolidatedEntriesStatusByDateAction
{
    private MonthlyTargetEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        MonthlyTargetEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function __invoke(MonthlyTargetEntriesData $consolidationEntriesData): array
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
