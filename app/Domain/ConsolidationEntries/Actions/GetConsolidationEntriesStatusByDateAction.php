<?php

namespace Domain\ConsolidationEntries\Actions;

use App\Domain\ConsolidationEntries\Constants\ReturnMessages;
use Domain\ConsolidationEntries\DataTransferObjects\ConsolidationEntriesData;
use Domain\ConsolidationEntries\Interfaces\ConsolidationEntriesRepositoryInterface;
use Domain\ConsolidationEntries\Models\ConsolidationEntries;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\ConsolidationEntries\ConsolidationEntriesRepository;

class GetConsolidationEntriesStatusByDateAction
{
    private ConsolidationEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        ConsolidationEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
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
