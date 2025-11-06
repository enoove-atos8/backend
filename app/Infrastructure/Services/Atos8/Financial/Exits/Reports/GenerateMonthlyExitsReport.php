<?php

namespace App\Infrastructure\Services\Atos8\Financial\Exits\Reports;

use App\Domain\Financial\Exits\Payments\Categories\DataTransferObjects\PaymentCategoryData;
use App\Domain\Financial\Exits\Payments\Items\DataTransferObjects\PaymentItemData;
use App\Domain\Financial\Reports\Exits\Actions\UpdateAmountsExitsReportRequestsAction;
use App\Domain\Financial\Reports\Exits\Actions\UpdateLinkExitsReportRequestsAction;
use App\Domain\Financial\Reports\Exits\Actions\UpdateStatusExitsReportRequestsAction;
use App\Domain\Financial\Reports\Exits\DataTransferObjects\MonthlyExitsReportData;
use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Carbon\Carbon;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Domain\Ecclesiastical\Divisions\Actions\GetDivisionsDataAction;
use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsAction;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountByIdAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Exception;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class GenerateMonthlyExitsReport
{
    private GetExitsAction $getExitsAction;

    private UpdateStatusExitsReportRequestsAction $updateStatusExitsReportRequestsAction;

    private UpdateLinkExitsReportRequestsAction $updateLinkExitsReportRequestsAction;

    private UpdateAmountsExitsReportRequestsAction $updateAmountsExitsReportRequestsAction;

    private GetAllGroupsAction $getAllGroupsAction;

    private GetDivisionsDataAction $getDivisionsDataAction;

    private GetChurchAction $getChurchAction;

    private GetAccountByIdAction $getAccountByIdAction;

    private UploadFile $uploadFile;

    private $groups;

    private $divisions;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage';

    const S3_PATH_MONTHLY_EXITS_REPORTS = 'reports/financial/exits/monthly_exits';

    const TENANTS_DIR = '/tenants';

    const REPORTS_TEMP_DIR = '/reports/temp';

    const PIX = 'pix';

    const CASH = 'cash';

    const MONTHLY_EXITS_BLADE_VIEW = 'reports/exits/monthlyExits/monthly_exits';

    const MONTHLY_EXITS_REPORT_NAME = 'monthly_exits.pdf';

    public function __construct(
        GetExitsAction $getExitsAction,
        UpdateStatusExitsReportRequestsAction $updateStatusExitsReportRequestsAction,
        UpdateLinkExitsReportRequestsAction $updateLinkExitsReportRequestsAction,
        UpdateAmountsExitsReportRequestsAction $updateAmountsExitsReportRequestsAction,
        UploadFile $uploadFile,
        GetChurchAction $getChurchAction,
        GetAccountByIdAction $getAccountByIdAction,
        GetAllGroupsAction $getAllGroupsAction,
        GetDivisionsDataAction $getDivisionsDataAction
    ) {
        $this->getExitsAction = $getExitsAction;
        $this->updateStatusExitsReportRequestsAction = $updateStatusExitsReportRequestsAction;
        $this->updateLinkExitsReportRequestsAction = $updateLinkExitsReportRequestsAction;
        $this->updateAmountsExitsReportRequestsAction = $updateAmountsExitsReportRequestsAction;
        $this->uploadFile = $uploadFile;
        $this->getChurchAction = $getChurchAction;
        $this->getAccountByIdAction = $getAccountByIdAction;
        $this->getAllGroupsAction = $getAllGroupsAction;
        $this->getDivisionsDataAction = $getDivisionsDataAction;
    }

    /**
     * Load all groups once to be reused across multiple functions.
     *
     * @throws Throwable
     */
    private function loadGroups(): void
    {
        $this->groups = $this->getAllGroupsAction->execute();
    }

    /**
     * Load all divisions once to be reused across multiple functions.
     *
     * @throws Throwable
     */
    private function loadDivisions(): void
    {
        $this->divisions = $this->getDivisionsDataAction->execute();
    }

    /**
     * Prepares general report data including church and account info.
     *
     * @throws Throwable
     */
    private function prepareGeneralReportData($exits, MonthlyExitsReportData $report, string $dates, string $tenant): object
    {
        $churchData = $this->getChurchAction->execute($tenant);
        $reportInfo = $this->getAccountByIdAction->execute($report->accountId);
        $totalExits = $exits->sum(ExitData::AMOUNT_PROPERTY);
        $quantity = $exits->count();

        return (object) [
            'churchData' => $churchData,
            'reportInfo' => $reportInfo,
            'generalData' => (object) [
                'period' => $dates,
                'generationDate' => Carbon::createFromFormat('Y-m-d H:i:s', $report->generationDate)->format('d/m/Y'),
                'totalExits' => $totalExits,
                'quantity' => $quantity,
            ],
        ];
    }

    /**
     * Prepares exits data with payments, contributions, ministerial transfer and transfers.
     *
     * @throws Throwable
     */
    private function prepareExitsData($exits, MonthlyExitsReportData $report): object
    {
        $totalPayments = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::PAYMENTS_VALUE)
            ->sum(ExitData::AMOUNT_PROPERTY);
        $qtdPayments = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::PAYMENTS_VALUE)
            ->count();

        $totalContributions = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::CONTRIBUTIONS_VALUE)
            ->sum(ExitData::AMOUNT_PROPERTY);
        $qtdContributions = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::CONTRIBUTIONS_VALUE)
            ->count();

        $totalMinisterialTransfer = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::MINISTERIAL_TRANSFER_VALUE)
            ->sum(ExitData::AMOUNT_PROPERTY);
        $qtdMinisterialTransfer = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::MINISTERIAL_TRANSFER_VALUE)
            ->count();

        $totalTransfer = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::TRANSFER_VALUE)
            ->sum(ExitData::AMOUNT_PROPERTY);
        $qtdTransfer = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::TRANSFER_VALUE)
            ->count();

        $totalAccountsTransfer = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::ACCOUNTS_TRANSFER_VALUE)
            ->sum(ExitData::AMOUNT_PROPERTY);

        return (object) [
            'payments' => (object) ['qtd' => $qtdPayments, 'total' => $totalPayments],
            'contributions' => (object) ['qtd' => $qtdContributions, 'total' => $totalContributions],
            'ministerialTransfer' => (object) ['qtd' => $qtdMinisterialTransfer, 'total' => $totalMinisterialTransfer],
            'transfer' => (object) ['qtd' => $qtdTransfer, 'total' => $totalTransfer],
            'totalAccountsTransfer' => $totalAccountsTransfer,
        ];
    }

    /**
     * Prepares payments data grouped by payment category and item.
     *
     * @throws Throwable
     */
    private function preparePaymentsData($exits): array
    {
        $payments = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::PAYMENTS_VALUE);

        $groupedByCategory = [];

        foreach ($payments as $exit) {
            $categoryName = $exit->{ExitData::PAYMENT_CATEGORY_PROPERTY}->{PaymentCategoryData::NAME_PROPERTY} ?? 'Sem Categoria';
            $itemName = $exit->{ExitData::PAYMENT_ITEM_PROPERTY}->{PaymentItemData::NAME_PROPERTY} ?? 'Sem Item';
            $amount = $exit->{ExitData::AMOUNT_PROPERTY};

            if (! isset($groupedByCategory[$categoryName])) {
                $groupedByCategory[$categoryName] = [
                    'categoryName' => $categoryName,
                    'items' => [],
                    'categoryTotal' => 0,
                    'categoryQtd' => 0,
                ];
            }

            if (! isset($groupedByCategory[$categoryName]['items'][$itemName])) {
                $groupedByCategory[$categoryName]['items'][$itemName] = (object) [
                    'itemName' => $itemName,
                    'qtd' => 0,
                    'total' => 0,
                ];
            }

            $groupedByCategory[$categoryName]['items'][$itemName]->qtd++;
            $groupedByCategory[$categoryName]['items'][$itemName]->total += $amount;
            $groupedByCategory[$categoryName]['categoryQtd']++;
            $groupedByCategory[$categoryName]['categoryTotal'] += $amount;
        }

        // Convert items arrays to objects
        foreach ($groupedByCategory as &$category) {
            $category['items'] = array_values($category['items']);
            $category = (object) $category;
        }

        return array_values($groupedByCategory);
    }

    /**
     * Prepares transfer data grouped by division.
     *
     * @throws Throwable
     */
    private function prepareTransferData($exits): array
    {
        $transfers = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::TRANSFER_VALUE);

        $groupedByDivision = [];

        foreach ($transfers as $exit) {
            $groupId = $exit->{ExitData::GROUP_PROPERTY}->{GroupData::ID_PROPERTY};
            $amount = $exit->{ExitData::AMOUNT_PROPERTY};

            // Find group and get division info
            $group = $this->groups->firstWhere(GroupsRepository::GROUP_ID_WITH_UNDERLINE, $groupId);
            $groupName = $group ? $group->{GroupData::GROUPS_NAME_PROPERTY} : 'Sem Grupo';
            $divisionId = $group ? $group->{GroupData::DIVISION_ID_PROPERTY} : null;

            // Find division
            $division = $this->divisions->firstWhere(DivisionData::ID_PROPERTY, $divisionId);
            $divisionName = $division ? $division->{DivisionData::NAME_PROPERTY} : 'Sem Divisão';

            if (! isset($groupedByDivision[$divisionId])) {
                $groupedByDivision[$divisionId] = [
                    'divisionName' => $divisionName,
                    'groups' => [],
                    'divisionTotal' => 0,
                    'divisionQtd' => 0,
                ];
            }

            if (! isset($groupedByDivision[$divisionId]['groups'][$groupId])) {
                $groupedByDivision[$divisionId]['groups'][$groupId] = (object) [
                    'groupName' => $groupName,
                    'qtd' => 0,
                    'total' => 0,
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
     * Prepares ministerial transfer data grouped by group.
     *
     * @throws Throwable
     */
    private function prepareMinisterialTransferData($exits): array
    {
        $ministerialTransfers = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::MINISTERIAL_TRANSFER_VALUE);

        $groupedData = [];

        foreach ($ministerialTransfers as $exit) {
            $groupId = $exit->{ExitData::GROUP_PROPERTY}->{GroupData::ID_PROPERTY};
            $amount = $exit->{ExitData::AMOUNT_PROPERTY};

            if (! isset($groupedData[$groupId])) {
                $group = $this->groups->firstWhere(GroupsRepository::GROUP_ID_WITH_UNDERLINE, $groupId);
                $groupName = $group ? $group->{GroupData::GROUPS_NAME_PROPERTY} : 'Sem Grupo';

                $groupedData[$groupId] = (object) [
                    'name' => $groupName,
                    'qtd' => 0,
                    'total' => 0,
                ];
            }

            $groupedData[$groupId]->qtd++;
            $groupedData[$groupId]->total += $amount;
        }

        return array_values($groupedData);
    }

    /**
     * Prepares contributions data grouped by group.
     *
     * @throws Throwable
     */
    private function prepareContributionsData($exits): array
    {
        $contributions = $exits->where(ExitData::EXIT_TYPE_PROPERTY, BaseRepository::OPERATORS['EQUALS'], ExitRepository::CONTRIBUTIONS_VALUE);

        $groupedData = [];

        foreach ($contributions as $exit) {
            $groupId = $exit->{ExitData::GROUP_PROPERTY}->{GroupData::ID_PROPERTY};
            $amount = $exit->{ExitData::AMOUNT_PROPERTY};

            if (! isset($groupedData[$groupId])) {
                $group = $this->groups->firstWhere(GroupsRepository::GROUP_ID_WITH_UNDERLINE, $groupId);
                $groupName = $group ? $group->{GroupData::GROUPS_NAME_PROPERTY} : 'Sem Grupo';

                $groupedData[$groupId] = (object) [
                    'name' => $groupName,
                    'qtd' => 0,
                    'total' => 0,
                ];
            }

            $groupedData[$groupId]->qtd++;
            $groupedData[$groupId]->total += $amount;
        }

        return array_values($groupedData);
    }

    public function cleanReportTempDir(string $directory): void
    {
        if (is_dir($directory)) {
            $files = scandir($directory);

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $directory.DIRECTORY_SEPARATOR.$file;

                    if (is_file($filePath)) {
                        unlink($filePath);
                    } elseif (is_dir($filePath)) {
                        $this->deleteDirectory($filePath);
                    }
                }
            }
        }
    }

    public function deleteDirectory($dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $dir.DIRECTORY_SEPARATOR.$file;
                if (is_dir($filePath)) {
                    $this->deleteDirectory($filePath);
                } else {
                    unlink($filePath);
                }
            }
        }
        rmdir($dir);
    }

    /**
     * @throws Throwable
     */
    public function execute(MonthlyExitsReportData $report, string $tenant): void
    {
        $dates = $report->dates[0];

        if (! is_null($dates)) {
            $timestamp = date('YmdHis');
            $directoryPath = self::STORAGE_BASE_PATH.self::TENANTS_DIR.'/'.$tenant.self::REPORTS_TEMP_DIR;

            if (! file_exists($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            $pdfPath = $directoryPath.'/'.$timestamp.'_'.self::MONTHLY_EXITS_REPORT_NAME;

            try {
                $exits = $this->getExitsAction->execute($dates, [], false)
                    ->where(ExitData::ACCOUNT_ID_PROPERTY, BaseRepository::OPERATORS['EQUALS'], $report->accountId);

                if ($exits->isEmpty()) {
                    $this->updateStatusExitsReportRequestsAction->execute($report->id, \App\Infrastructure\Repositories\Financial\Reports\Exits\MonthlyExitsReportsRepository::NO_DATA_STATUS_VALUE);

                    return;
                }

                $this->loadGroups();
                $this->loadDivisions();

                $reportDataInfo = $this->prepareGeneralReportData($exits, $report, $dates, $tenant);
                $exitsData = $this->prepareExitsData($exits, $report);
                $paymentsData = $this->preparePaymentsData($exits);
                $transferData = $this->prepareTransferData($exits);
                $ministerialTransferData = $this->prepareMinisterialTransferData($exits);
                $contributionsData = $this->prepareContributionsData($exits);

                $reportData = (object) [
                    'churchData' => $reportDataInfo->churchData,
                    'reportInfo' => $reportDataInfo->reportInfo,
                    'generalReportData' => $reportDataInfo->generalData,
                    'exitsData' => $exitsData,
                    'paymentsData' => $paymentsData,
                    'transferData' => $transferData,
                    'ministerialTransferData' => $ministerialTransferData,
                    'contributionsData' => $contributionsData,
                ];

                $viewData = [
                    'reportData' => $reportData,
                    'monthlyExitsReportObject' => $report,
                ];

                $view = view(self::MONTHLY_EXITS_BLADE_VIEW, $viewData)->render();
                PDFGenerator::save($view, $pdfPath);

                $linkReport = $this->uploadFile->upload($pdfPath, self::S3_PATH_MONTHLY_EXITS_REPORTS, $tenant);
                $this->updateLinkExitsReportRequestsAction->execute($report->id, $linkReport);

                $totalAmount = $reportDataInfo->generalData->totalExits;
                $this->updateAmountsExitsReportRequestsAction->execute($report->id, $totalAmount);

                $this->cleanReportTempDir(self::STORAGE_BASE_PATH.self::TENANTS_DIR.'/'.$tenant.self::REPORTS_TEMP_DIR);

                $this->updateStatusExitsReportRequestsAction->execute($report->id, \App\Infrastructure\Repositories\Financial\Reports\Exits\MonthlyExitsReportsRepository::DONE_STATUS_VALUE);

            } catch (Exception $e) {
                throw new GeneralExceptions(
                    'Houve um erro ao gerar o relatório: '.$e->getMessage(),
                    500
                );
            }
        } else {
            $this->updateStatusExitsReportRequestsAction->execute($report->id, \App\Infrastructure\Repositories\Financial\Reports\Exits\MonthlyExitsReportsRepository::ERROR_STATUS_VALUE);
        }
    }
}
