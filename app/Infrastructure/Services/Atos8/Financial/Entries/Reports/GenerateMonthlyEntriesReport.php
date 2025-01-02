<?php

namespace App\Infrastructure\Services\Atos8\Financial\Entries\Reports;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Infrastructure\Repositories\Financial\Entries\Reports\ReportRequestsRepository;
use Throwable;

class GenerateMonthlyEntriesReport
{

    /**
     * @throws Throwable
     */
    public function __invoke(): void
    {

    }
}
