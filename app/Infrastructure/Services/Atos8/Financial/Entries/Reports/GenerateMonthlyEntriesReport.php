<?php

namespace App\Infrastructure\Services\Atos8\Financial\Entries\Reports;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\MonthlyReportData;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use Domain\Ecclesiastical\Groups\Actions\GetGroupsByIdAction;
use Domain\Financial\Entries\Reports\Actions\UpdateAmountsReportRequestsAction;
use Domain\Financial\Entries\Reports\Actions\UpdateLinkReportRequestsAction;
use Domain\Financial\Entries\Reports\Actions\UpdateStatusReportRequestsAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Infrastructure\Repositories\Financial\Entries\Reports\MonthlyReportsRepository;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class GenerateMonthlyEntriesReport
{

    private array $linkReceiptList = [];
    private GetEntriesAction $getEntriesAction;

    private UpdateStatusReportRequestsAction $updateStatusReportRequestsAction;
    private UpdateLinkReportRequestsAction $updateLinkReportRequestsAction;
    private UpdateAmountsReportRequestsAction $updateAmountsReportRequestsAction;
    private GetGroupsByIdAction $getGroupsByIdAction;
    private UploadFile $uploadFile;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';
    const PATH_ENTRIES_MONTHLY_ENTRIES_REPORTS = 'entries/reports/monthly_entries';
    const TENANTS_DIR = '/tenants';
    const REPORTS_TEMP_DIR = '/reports/temp/';
    const PIX = 'pix';
    const CASH = 'cash';

    const MONTHLY_ENTRIES_BLADE_VIEW = 'reports/entries/monthlyEntries/monthly_entries';
    const MONTHLY_ENTRIES_REPORT_NAME = 'monthly_entries.pdf';

    public function __construct(
        GetEntriesAction $getEntriesAction,
        UpdateStatusReportRequestsAction $updateStatusReportRequestsAction,
        UpdateLinkReportRequestsAction $updateLinkReportRequestsAction,
        GetGroupsByIdAction $getGroupsByIdAction,
        UpdateAmountsReportRequestsAction $updateAmountsReportRequestsAction,
        UploadFile $uploadFile
    )
    {
        $this->getEntriesAction = $getEntriesAction;
        $this->updateStatusReportRequestsAction = $updateStatusReportRequestsAction;
        $this->updateLinkReportRequestsAction = $updateLinkReportRequestsAction;
        $this->getGroupsByIdAction = $getGroupsByIdAction;
        $this->updateAmountsReportRequestsAction = $updateAmountsReportRequestsAction;
        $this->uploadFile = $uploadFile;
    }

    /**
     * @throws Throwable
     */
    public function execute(MonthlyReportData $report, string $tenant): void
    {

    }
}
