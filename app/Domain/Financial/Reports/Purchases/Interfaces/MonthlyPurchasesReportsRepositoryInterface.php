<?php

namespace App\Domain\Financial\Reports\Purchases\Interfaces;

use App\Domain\Financial\Reports\Purchases\DataTransferObjects\MonthlyPurchasesReportData;
use App\Domain\Financial\Reports\Purchases\Models\PurchasesReportRequests;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface MonthlyPurchasesReportsRepositoryInterface
{
    public function generateMonthlyPurchasesReport(MonthlyPurchasesReportData $monthlyPurchasesReportData): PurchasesReportRequests;
    public function generateMonthlyReceiptsPurchaseReport(MonthlyPurchasesReportData $monthlyPurchasesReportData): PurchasesReportRequests;
    public function getReports(bool $paginate): Collection | Paginator;
    public function getReportsByStatus(string $status): Collection;
    public function updateStatus($id, string $status): mixed;
    public function updateLinkReport($id, string $link): mixed;
    public function updatePurchaseAmount($id, float $amount): mixed;
}
