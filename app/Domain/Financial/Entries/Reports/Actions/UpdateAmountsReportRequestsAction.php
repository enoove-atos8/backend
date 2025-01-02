<?php

namespace Domain\Financial\Entries\Reports\Actions;

use App\Domain\Financial\Entries\Reports\Constants\ReturnMessages;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\ReportRequestsData;
use App\Domain\Financial\Entries\Reports\Interfaces\ReportRequestsRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Reports\ReportRequestsRepository;

class UpdateAmountsReportRequestsAction
{
    private ReportRequestsRepository $reportRequestsRepository;

    public function __construct(ReportRequestsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     * @throws GeneralExceptions
     */
    public function __invoke($id, array $entryTypesAmount): bool
    {
        $updatedEntryTypesAmounts = $this->reportRequestsRepository->updateEntryTypesAmount($id, $entryTypesAmount);

        if($updatedEntryTypesAmounts)
            return true;
        else
            throw new GeneralExceptions(ReturnMessages::ERROR_AMOUNTS_UPDATE, 500);
    }
}
