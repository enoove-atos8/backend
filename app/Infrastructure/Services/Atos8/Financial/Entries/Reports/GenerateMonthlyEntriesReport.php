<?php

namespace App\Infrastructure\Services\Atos8\Financial\Entries\Reports;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Entries\Reports\Actions\UpdateMonthlyEntriesAmountAction;
use App\Domain\Financial\Entries\Reports\DataTransferObjects\MonthlyReportData;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Carbon\Carbon;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionsDataAction;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsAction;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountByIdAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Financial\Entries\Reports\MonthlyReportsRepository;
use Spatie\Browsershot\Browsershot;
use Exception;
use Illuminate\Support\Facades\Log;
use Domain\Ecclesiastical\Groups\Actions\GetGroupsByIdAction;
use Domain\Financial\Entries\Reports\Actions\UpdateAmountsReportRequestsAction;
use Domain\Financial\Entries\Reports\Actions\UpdateLinkReportRequestsAction;
use Domain\Financial\Entries\Reports\Actions\UpdateStatusReportRequestsAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class GenerateMonthlyEntriesReport
{

    private array $linkReceiptList = [];
    private GetEntriesAction $getEntriesAction;
    private UpdateStatusReportRequestsAction $updateStatusReportRequestsAction;
    private UpdateLinkReportRequestsAction $updateLinkReportRequestsAction;
    private UpdateAmountsReportRequestsAction $updateAmountsReportRequestsAction;

    private UpdateMonthlyEntriesAmountAction $updateMonthlyEntriesAmountAction;
    private GetGroupsByIdAction $getGroupsByIdAction;
    private GetAllGroupsAction $getAllGroupsAction;
    private GetDivisionsDataAction $getDivisionsDataAction;
    private GetChurchAction $getChurchAction;
    private GetAccountByIdAction $getAccountByIdAction;
    private UploadFile $uploadFile;
    private $groups;
    private $divisions;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage';
    const S3_PATH_MONTHLY_ENTRIES_REPORTS = 'reports/financial/entries/monthly_entries';
    const TENANTS_DIR = '/tenants';
    const REPORTS_TEMP_DIR = '/reports/temp';
    const PIX = 'pix';
    const CASH = 'cash';

    const MONTHLY_ENTRIES_BLADE_VIEW = 'reports/entries/monthlyEntries/monthly_entries';
    const MONTHLY_ENTRIES_REPORT_NAME = 'monthly_entries.pdf';

    // Height calculation constants (in pixels, 96 DPI)
    const PAGE_HEIGHT_PX = 1123; // A4 height at 96 DPI
    const MARGINS_PX = 76; // 10mm top + 10mm bottom margins
    const FIRST_DIV_HEIGHT_PX = 333; // First div with 3 cards

    public function __construct(
        GetEntriesAction $getEntriesAction,
        UpdateStatusReportRequestsAction $updateStatusReportRequestsAction,
        UpdateLinkReportRequestsAction $updateLinkReportRequestsAction,
        GetGroupsByIdAction $getGroupsByIdAction,
        UpdateAmountsReportRequestsAction $updateAmountsReportRequestsAction,
        UploadFile $uploadFile,
        GetChurchAction $getChurchAction,
        GetAccountByIdAction $getAccountByIdAction,
        GetAllGroupsAction $getAllGroupsAction,
        GetDivisionsDataAction $getDivisionsDataAction,
        UpdateMonthlyEntriesAmountAction $updateMonthlyEntriesAmountAction
    )
    {
        $this->getEntriesAction = $getEntriesAction;
        $this->updateStatusReportRequestsAction = $updateStatusReportRequestsAction;
        $this->updateLinkReportRequestsAction = $updateLinkReportRequestsAction;
        $this->getGroupsByIdAction = $getGroupsByIdAction;
        $this->updateAmountsReportRequestsAction = $updateAmountsReportRequestsAction;
        $this->uploadFile = $uploadFile;
        $this->getChurchAction = $getChurchAction;
        $this->getAccountByIdAction = $getAccountByIdAction;
        $this->getAllGroupsAction = $getAllGroupsAction;
        $this->getDivisionsDataAction = $getDivisionsDataAction;
        $this->updateMonthlyEntriesAmountAction = $updateMonthlyEntriesAmountAction;
    }

    /**
     * Load all groups once to be reused across multiple functions.
     *
     * @return void
     * @throws Throwable
     */
    private function loadGroups(): void
    {
        $this->groups = $this->getAllGroupsAction->execute();
    }

    /**
     * Load all divisions once to be reused across multiple functions.
     *
     * @return void
     * @throws Throwable
     */
    private function loadDivisions(): void
    {
        $this->divisions = $this->getDivisionsDataAction->execute();
    }

    /**
     * Calculates dynamic table height based on number of rows.
     *
     * @param int $rowCount Number of data rows
     * @return int Height in pixels
     */
    private function calculateTableHeight(int $rowCount): int
    {
        $headerHeight = 60; // Table header
        $rowHeight = 45; // Each row
        $totalRowHeight = 60; // Total row at bottom
        $padding = 32; // Top and bottom padding

        return $headerHeight + ($rowCount * $rowHeight) + $totalRowHeight + $padding;
    }



    /**
     * Calculates spacer height needed to push table to next page.
     *
     * @param int $usedSpace Space already used on current page
     * @param int $tableHeight Height of the table to check
     * @return int Spacer height in pixels (0 if no spacer needed)
     */
    private function calculateSpacerHeight(int $usedSpace, int $tableHeight): int
    {
        $availableSpace = self::PAGE_HEIGHT_PX - self::MARGINS_PX - $usedSpace;

        // If table fits, no spacer needed
        if ($tableHeight <= $availableSpace) {
            return 0;
        }

        // Return exact height needed to fill the page
        return $availableSpace;
    }

    /**
     * Prepares general report data including church and account info.
     *
     * @param $entries
     * @param MonthlyReportData $report
     * @param string $dates
     * @param string $tenant
     * @return object
     * @throws Throwable
     */
    private function prepareGeneralReportData($entries, MonthlyReportData $report, string $dates, string $tenant): object
    {
        $churchData = $this->getChurchAction->execute($tenant);
        $reportInfo = $this->getAccountByIdAction->execute($report->accountId);
        $totalEntries = $entries->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);
        $quantity = $entries->count();

        return (object) [
            'churchData' => $churchData,
            'reportInfo' => $reportInfo,
            'generalData' => (object) [
                'period' => $dates,
                'generationDate' => Carbon::createFromFormat('Y-m-d H:i:s', $report->generationDate)->format('d/m/Y'),
                'totalEntries' => $totalEntries,
                'quantity' => $quantity,
            ]
        ];
    }

    /**
     * Prepares entries data with tithes, offers and designated entries.
     *
     * @param $entries
     * @param MonthlyReportData $report
     * @return object
     * @throws Throwable
     */
    private function prepareEntriesData($entries, MonthlyReportData $report): object
    {
        $totalTithes = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::TITHE_VALUE)
            ->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);
        $qtdTithes = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::TITHE_VALUE)
            ->count();

        $totalOffers = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::OFFER_VALUE)
            ->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);
        $qtdOffers = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::OFFER_VALUE)
            ->count();

        $totalDesignated = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::DESIGNATED_VALUE)
            ->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);
        $qtdDesignated = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::DESIGNATED_VALUE)
            ->count();

        // Get anonymous offers from entries (created automatically during movements import)
        $totalAnonymousOffers = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::ANONYMOUS_OFFERS_VALUE)
            ->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);

        $totalAccountsTransfer = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::ACCOUNTS_TRANSFER_VALUE)
            ->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);

        return (object) [
            'tithes' => (object) ['qtd' => $qtdTithes, 'total' => $totalTithes],
            'offers' => (object) ['qtd' => $qtdOffers, 'total' => $totalOffers],
            'designated' => (object) ['qtd' => $qtdDesignated, 'total' => $totalDesignated],
            'anonymousAmount' => $totalAnonymousOffers,
            'totalAccountsTransfer' => $totalAccountsTransfer,
        ];
    }


    /**
     * Prepares designated entries data grouped by division and group.
     *
     * @param $entries
     * @return array
     * @throws Throwable
     */
    private function prepareDesignatedData($entries): array
    {
        $designated = $entries->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::DESIGNATED_VALUE);

        $groupedByDivision = [];

        foreach ($designated as $entry) {
            $groupId = $entry->entries_group_received_id;
            $amount = $entry->entries_amount;

            // Find group and get division info
            $group = $this->groups->firstWhere(GroupsRepository::GROUP_ID_WITH_UNDERLINE, $groupId);
            $groupName = $group ? $group->{GroupData::GROUPS_NAME_PROPERTY} : 'Sem Grupo';
            $divisionId = $group ? $group->{GroupData::DIVISION_ID_PROPERTY} : null;

            // Find division
            $division = $this->divisions->firstWhere(DivisionData::ID_PROPERTY, $divisionId);
            $divisionName = $division ? $division->{DivisionData::NAME_PROPERTY} : 'Sem Divisão';

            if (!isset($groupedByDivision[$divisionId])) {
                $groupedByDivision[$divisionId] = [
                    'divisionName' => $divisionName,
                    'groups' => [],
                    'divisionTotal' => 0,
                    'divisionQtd' => 0
                ];
            }

            if (!isset($groupedByDivision[$divisionId]['groups'][$groupId])) {
                $groupedByDivision[$divisionId]['groups'][$groupId] = (object) [
                    'groupName' => $groupName,
                    'qtd' => 0,
                    'total' => 0
                ];
            }

            $groupedByDivision[$divisionId]['groups'][$groupId]->qtd++;
            $groupedByDivision[$divisionId]['groups'][$groupId]->total += $amount;
            $groupedByDivision[$divisionId]['divisionQtd']++;
            $groupedByDivision[$divisionId]['divisionTotal'] += $amount;
        }

        // Convert groups arrays to objects
        foreach ($groupedByDivision as &$division) {
            $division['groups'] = array_values($division['groups']);
            $division = (object) $division;
        }

        return array_values($groupedByDivision);
    }

    /**
     * Prepares data for page break calculations.
     *
     * @param MonthlyReportData $report
     * @param array $designatedEntriesData Data for designated entries table (ENTRADAS DESIGNADAS)
     * @return array
     */
    private function prepareReportData(
        MonthlyReportData $report,
        array $designatedEntriesData = []
    ): array
    {
        $entriesTableRows = 3
            + ($report->includeAnonymousOffers ? 1 : 0)
            + ($report->includeTransfersBetweenAccounts ? 1 : 0);

        $entriesTableHeight = $this->calculateTableHeight($entriesTableRows);

        $usedSpaceAfterEntriesTable = self::FIRST_DIV_HEIGHT_PX + $entriesTableHeight;

        $designatedEntriesTableRows = count($designatedEntriesData);
        $designatedEntriesTableHeight = $this->calculateTableHeight($designatedEntriesTableRows);

        $designatedEntriesTableSpacerHeight = $this->calculateSpacerHeight(
            $usedSpaceAfterEntriesTable,
            $designatedEntriesTableHeight
        );

        return [
            'designatedEntriesTableSpacerHeight' => $designatedEntriesTableSpacerHeight,
        ];
    }



    /**
     * @param string $directory
     * @return void
     */
    public function cleanReportTempDir(string $directory): void
    {
        if (is_dir($directory))
        {
            $files = scandir($directory);

            foreach ($files as $file) {
                if ($file !== "." && $file !== "..")
                {
                    $filePath = $directory . DIRECTORY_SEPARATOR . $file;

                    if (is_file($filePath))
                        unlink($filePath);
                    elseif (is_dir($filePath))
                        $this->deleteDirectory($filePath);
                }
            }
        }
    }


    /**
     * @param $dir
     * @return void
     */
    function deleteDirectory($dir): void
    {
        if (!is_dir($dir)) return;

        foreach (scandir($dir) as $file) {
            if ($file !== "." && $file !== "..") {
                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($filePath)) {
                    $this->deleteDirectory($filePath);
                } else {
                    unlink($filePath);
                }
            }
        }
        rmdir($dir); // Remove o diretório após esvaziá-lo
    }



    /**
     * @throws Throwable
     */
    public function execute(MonthlyReportData $report, string $tenant): void
    {
        $dates = $report->dates[0];

        if(!is_null($dates))
        {
            $timestamp = date('YmdHis');
            $directoryPath = self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR;

            if (!file_exists($directoryPath))
                mkdir($directoryPath, 0775, true);

            $pdfPath = $directoryPath . '/' . $timestamp . '_' . self::MONTHLY_ENTRIES_REPORT_NAME;

            try
            {
                $entries = $this->getEntriesAction->execute($dates, [], false)
                    ->where(EntryRepository::ACCOUNT_ID_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], $report->accountId);

                $this->loadGroups();
                $this->loadDivisions();

                $reportDataInfo = $this->prepareGeneralReportData($entries, $report, $dates, $tenant);
                $entriesData = $this->prepareEntriesData($entries, $report);
                $designatedEntriesData = $this->prepareDesignatedData($entries);
                $totalsData = [];

                $heightsTableConfig = $this->prepareReportData($report, $designatedEntriesData);

                $reportData = (object) [
                    'churchData' => $reportDataInfo->churchData,
                    'reportInfo' => $reportDataInfo->reportInfo,
                    'generalReportData' => $reportDataInfo->generalData,
                    'entriesData' => $entriesData,
                    'designatedEntriesData' => $designatedEntriesData,
                    'totalsData' => $totalsData,
                ];

                $viewData = array_merge($heightsTableConfig, [
                    'reportData' => $reportData,
                    'monthlyReportObject' => $report,
                ]);

                $view = view(self::MONTHLY_ENTRIES_BLADE_VIEW, $viewData)->render();
                PdfGenerator::save($view, $pdfPath);

                $linkReport = $this->uploadFile->upload($pdfPath, self::S3_PATH_MONTHLY_ENTRIES_REPORTS, $tenant);
                $this->updateLinkReportRequestsAction->execute($report->id, $linkReport);

                $this->cleanReportTempDir(self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR);

                $this->updateMonthlyEntriesAmountAction->execute($report->id, $entries->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS));
                $this->updateStatusReportRequestsAction->execute($report->id, MonthlyReportsRepository::DONE_STATUS_VALUE);

            }
            catch (Exception $e)
            {
                throw new GeneralExceptions(
                    'Houve um erro ao gerar o relatório: ' . $e->getMessage(),
                    500
                );
            }
        }
        else
        {
            $this->updateStatusReportRequestsAction->execute($report->id, MonthlyReportsRepository::ERROR_STATUS_VALUE);
        }
    }
}
