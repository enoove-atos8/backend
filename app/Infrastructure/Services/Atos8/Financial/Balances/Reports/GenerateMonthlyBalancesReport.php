<?php

namespace App\Infrastructure\Services\Atos8\Financial\Balances\Reports;

use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Balances\GetBalanceByAccountAndDateAction;
use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Reports\Balances\Actions\UpdateLinkBalancesReportRequestsAction;
use App\Domain\Financial\Reports\Balances\Actions\UpdateStatusBalancesReportRequestsAction;
use App\Domain\Financial\Reports\Balances\DataTransferObjects\MonthlyBalancesReportData;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use App\Infrastructure\Repositories\Financial\Reports\Balances\MonthlyBalancesReportsRepository;
use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Carbon\Carbon;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountByIdAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Exception;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class GenerateMonthlyBalancesReport
{
    private GetEntriesAction $getEntriesAction;

    private GetExitsAction $getExitsAction;

    private UpdateStatusBalancesReportRequestsAction $updateStatusBalancesReportRequestsAction;

    private UpdateLinkBalancesReportRequestsAction $updateLinkBalancesReportRequestsAction;

    private GetChurchAction $getChurchAction;

    private GetAccountByIdAction $getAccountByIdAction;

    private GetBalanceByAccountAndDateAction $getBalanceByAccountAndDateAction;

    private UploadFile $uploadFile;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage';

    const S3_PATH_MONTHLY_BALANCES_REPORTS = 'reports/financial/balances/monthly_balances';

    const TENANTS_DIR = '/tenants';

    const REPORTS_TEMP_DIR = '/reports/temp';

    const MONTHLY_BALANCES_BLADE_VIEW = 'reports/balances/monthlyBalances/monthly_balances';

    const MONTHLY_BALANCES_REPORT_NAME = 'monthly_balances.pdf';

    public function __construct(
        GetEntriesAction $getEntriesAction,
        GetExitsAction $getExitsAction,
        UpdateStatusBalancesReportRequestsAction $updateStatusBalancesReportRequestsAction,
        UpdateLinkBalancesReportRequestsAction $updateLinkBalancesReportRequestsAction,
        UploadFile $uploadFile,
        GetChurchAction $getChurchAction,
        GetAccountByIdAction $getAccountByIdAction,
        GetBalanceByAccountAndDateAction $getBalanceByAccountAndDateAction
    ) {
        $this->getEntriesAction = $getEntriesAction;
        $this->getExitsAction = $getExitsAction;
        $this->updateStatusBalancesReportRequestsAction = $updateStatusBalancesReportRequestsAction;
        $this->updateLinkBalancesReportRequestsAction = $updateLinkBalancesReportRequestsAction;
        $this->uploadFile = $uploadFile;
        $this->getChurchAction = $getChurchAction;
        $this->getAccountByIdAction = $getAccountByIdAction;
        $this->getBalanceByAccountAndDateAction = $getBalanceByAccountAndDateAction;
    }

    /**
     * Prepares general report data including church and account info.
     *
     * @throws Throwable
     */
    private function prepareGeneralReportData(MonthlyBalancesReportData $report, string $dates, string $tenant): object
    {
        $churchData = $this->getChurchAction->execute($tenant);
        $reportInfo = $this->getAccountByIdAction->execute($report->accountId);

        return (object) [
            'churchData' => $churchData,
            'reportInfo' => $reportInfo,
            'generalData' => (object) [
                'period' => $dates,
                'generationDate' => Carbon::createFromFormat('Y-m-d H:i:s', $report->generationDate)->format('d/m/Y'),
            ],
        ];
    }

    /**
     * Get balance data for the report
     * The balance should already be calculated and saved in accounts_balances
     * by ProcessAccountFileJob when the bank statement was processed
     *
     * @throws Throwable
     */
    private function getBalanceData(int $accountId, string $referenceDate): object
    {
        // Buscar saldo já calculado na tabela accounts_balances
        $balance = $this->getBalanceByAccountAndDateAction->execute($accountId, $referenceDate);

        // Se não existe saldo calculado, retornar zeros
        if (! $balance) {
            return (object) [
                'totalEntries' => 0,
                'totalExits' => 0,
                'previousBalance' => 0,
                'currentBalance' => 0,
            ];
        }

        // Buscar entradas do mês para exibir no relatório
        $entries = $this->getEntriesAction->execute($referenceDate, [], false)
            ->where(EntryRepository::ACCOUNT_ID_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], $accountId);

        $totalEntries = $entries->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);

        // Buscar saídas do mês para exibir no relatório
        $exits = $this->getExitsAction->execute($referenceDate, [], false)
            ->where(ExitData::ACCOUNT_ID_PROPERTY, BaseRepository::OPERATORS['EQUALS'], $accountId);

        $totalExits = $exits->sum(ExitData::AMOUNT_PROPERTY);

        return (object) [
            'totalEntries' => $totalEntries,
            'totalExits' => $totalExits,
            'previousBalance' => $balance->previousMonthBalance,
            'currentBalance' => $balance->currentMonthBalance,
        ];
    }

    /**
     * Clean the temporary directory after report generation.
     */
    private function cleanReportTempDir(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            unlink("$dir/$file");
        }
        rmdir($dir);
    }

    /**
     * @throws Throwable
     */
    public function execute(MonthlyBalancesReportData $report, string $tenant): void
    {
        $dates = $report->dates[0];

        if (! is_null($dates)) {
            $timestamp = date('YmdHis');
            $directoryPath = self::STORAGE_BASE_PATH.self::TENANTS_DIR.'/'.$tenant.self::REPORTS_TEMP_DIR;

            if (! file_exists($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            $pdfPath = $directoryPath.'/'.$timestamp.'_'.self::MONTHLY_BALANCES_REPORT_NAME;

            try {
                // Obter dados de saldo já calculados (calculados pelo ProcessAccountFileJob)
                $balancesData = $this->getBalanceData($report->accountId, $dates);

                // Se não houver movimentações
                if ($balancesData->totalEntries == 0 && $balancesData->totalExits == 0 && $balancesData->previousBalance == 0) {
                    $this->updateStatusBalancesReportRequestsAction->execute($report->id, MonthlyBalancesReportsRepository::NO_DATA_STATUS_VALUE);

                    return;
                }

                $reportDataInfo = $this->prepareGeneralReportData($report, $dates, $tenant);

                $reportData = (object) [
                    'churchData' => $reportDataInfo->churchData,
                    'reportInfo' => $reportDataInfo->reportInfo,
                    'generalReportData' => $reportDataInfo->generalData,
                    'balancesData' => $balancesData,
                ];

                $viewData = [
                    'reportData' => $reportData,
                    'monthlyBalancesReportObject' => $report,
                ];

                $view = view(self::MONTHLY_BALANCES_BLADE_VIEW, $viewData)->render();
                PDFGenerator::save($view, $pdfPath);

                $linkReport = $this->uploadFile->upload($pdfPath, self::S3_PATH_MONTHLY_BALANCES_REPORTS, $tenant);
                $this->updateLinkBalancesReportRequestsAction->execute($report->id, $linkReport);

                $this->cleanReportTempDir(self::STORAGE_BASE_PATH.self::TENANTS_DIR.'/'.$tenant.self::REPORTS_TEMP_DIR);

                $this->updateStatusBalancesReportRequestsAction->execute($report->id, MonthlyBalancesReportsRepository::DONE_STATUS_VALUE);

            } catch (Exception $e) {
                throw new GeneralExceptions(
                    'Houve um erro ao gerar o relatório: '.$e->getMessage(),
                    500
                );
            }
        } else {
            $this->updateStatusBalancesReportRequestsAction->execute($report->id, MonthlyBalancesReportsRepository::ERROR_STATUS_VALUE);
        }
    }
}
