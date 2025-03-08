<?php

namespace Domain\Financial\Entries\Consolidation\Actions;

use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class CheckConsolidationStatusAction
{
    private ConsolidationRepository $consolidationRepository;

    public function __construct(ConsolidatedEntriesRepositoryInterface $consolidationRepositoryInterface)
    {
        $this->consolidationRepository = $consolidationRepositoryInterface;
    }


    /**
     * @throws BindingResolutionException
     */
    public function execute(string $date): bool
    {
        $dateTime = DateTime::createFromFormat('d/m/Y', $date);
        $formatDate = $dateTime->format('Y-m');

        $isDataConsolidated = $this->consolidationRepository->checkConsolidationStatus($formatDate);

        if(!is_null($isDataConsolidated))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
