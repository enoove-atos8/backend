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

class UpdateLinkReportRequestsAction
{
    private ReportRequestsRepository $reportRequestsRepository;

    public function __construct(ReportRequestsRepositoryInterface $reportRequestsRepositoryInterface)
    {
        $this->reportRequestsRepository = $reportRequestsRepositoryInterface;
    }


    /**
     */
    public function __invoke($id, string $link): bool
    {
        return $this->reportRequestsRepository->updateLinkReport($id, $link);
    }
}
