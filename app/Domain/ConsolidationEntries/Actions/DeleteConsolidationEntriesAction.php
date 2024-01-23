<?php

namespace Domain\ConsolidationEntries\Actions;
use Domain\ConsolidationEntries\Constants\ReturnMessages;
use Domain\ConsolidationEntries\Interfaces\ConsolidationEntriesRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\ConsolidationEntries\ConsolidationEntriesRepository;

class DeleteConsolidationEntriesAction
{
    private ConsolidationEntriesRepository $consolidationEntriesRepository;

    public function __construct(
        ConsolidationEntriesRepositoryInterface $consolidationEntriesRepositoryInterface
    )
    {
        $this->consolidationEntriesRepository = $consolidationEntriesRepositoryInterface;
    }


    /**
     * @param string $date
     * @return bool
     * @throws GeneralExceptions
     */
    public function __invoke(string $date): bool
    {
        $response = $this->consolidationEntriesRepository->deleteConsolidationEntry($date);

        if($response)
            return true;
        else
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_ENTRIES_CONSOLIDATED, 500);
    }

}
