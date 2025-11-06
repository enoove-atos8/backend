<?php

namespace App\Domain\Financial\Reports\Balances\Interfaces;

use App\Domain\Financial\Reports\Balances\DataTransferObjects\MonthlyBalancesReportData;
use App\Domain\Financial\Reports\Balances\Models\BalancesReportRequests;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface MonthlyBalancesReportsRepositoryInterface
{
    public function generateMonthlyBalancesReport(MonthlyBalancesReportData $monthlyBalancesReportData): BalancesReportRequests;

    public function getReports(bool $paginate): Collection|Paginator;

    public function getReportsByStatus(string $status): Collection;

    public function updateStatus($id, string $status): mixed;

    public function updateLinkReport($id, string $link): mixed;
}
