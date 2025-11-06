<?php

namespace App\Domain\Financial\Reports\Entries\Interfaces;

use App\Domain\Financial\Reports\Entries\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Reports\Entries\Models\ReportRequests;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface MonthlyReportsRepositoryInterface
{
    public function generateMonthlyReceiptsReport(MonthlyReportData $monthlyReportData): ReportRequests;
    public function generateMonthlyEntriesReport(MonthlyReportData $monthlyReportData): ReportRequests;
    public function getReports(bool $paginate): Collection | Paginator;
    public function getReportsByStatus(string $status): Collection;
    public function updateStatus($id, string $status): mixed;
    public function updateLinkReport($id, string $link): mixed;
    public function updateEntryTypesAmount($id, array $entryTypesAmount): mixed;

    public function updateMonthlyEntriesAmount($id, string $amount): mixed;
}
