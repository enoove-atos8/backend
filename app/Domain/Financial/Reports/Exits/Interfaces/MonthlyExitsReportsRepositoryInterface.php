<?php

namespace App\Domain\Financial\Reports\Exits\Interfaces;

use App\Domain\Financial\Reports\Exits\DataTransferObjects\MonthlyExitsReportData;
use App\Domain\Financial\Reports\Exits\Models\ExitsReportRequests;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface MonthlyExitsReportsRepositoryInterface
{
    public function generateMonthlyExitsReport(MonthlyExitsReportData $monthlyExitsReportData): ExitsReportRequests;
    public function generateMonthlyExitsReceiptsReport(MonthlyExitsReportData $monthlyExitsReportData): ExitsReportRequests;
    public function getReports(bool $paginate): Collection | Paginator;
    public function getReportsByStatus(string $status): Collection;
    public function updateStatus($id, string $status): mixed;
    public function updateLinkReport($id, string $link): mixed;
    public function updateExitAmount($id, float $amount): mixed;
}
