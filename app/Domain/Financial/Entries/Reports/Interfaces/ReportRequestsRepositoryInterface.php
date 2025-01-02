<?php

namespace App\Domain\Financial\Entries\Reports\Interfaces;

use App\Domain\Financial\Entries\Reports\DataTransferObjects\ReportRequestsData;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface ReportRequestsRepositoryInterface
{
    public function generateReport(ReportRequestsData $reportJobData): ReportRequests;

    public function getReports(): Collection;
    public function updateStatus($id, string $status): mixed;
    public function updateLinkReport($id, string $link): mixed;
}
