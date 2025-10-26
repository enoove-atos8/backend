<?php

namespace App\Infrastructure\Services\Atos8\Financial\Exits\Reports;

use App\Domain\Financial\Exits\Reports\DataTransferObjects\MonthlyExitsReportData;
use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Domain\Financial\Exits\Exits\Actions\GetExitsAction;
use Domain\Financial\Exits\Reports\Actions\UpdateAmountsExitsReportRequestsAction;
use Domain\Financial\Exits\Reports\Actions\UpdateLinkExitsReportRequestsAction;
use Domain\Financial\Exits\Reports\Actions\UpdateStatusExitsReportRequestsAction;
use Exception;
use Illuminate\Support\Facades\Http;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Infrastructure\Repositories\Financial\Exits\Reports\MonthlyExitsReportsRepository;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class GenerateMonthlyReceiptsReport
{
    private GetExitsAction $getExitsAction;

    private UpdateStatusExitsReportRequestsAction $updateStatusExitsReportRequestsAction;
    private UpdateLinkExitsReportRequestsAction $updateLinkExitsReportRequestsAction;
    private UpdateAmountsExitsReportRequestsAction $updateAmountsExitsReportRequestsAction;

    private GetChurchAction $getChurchAction;
    private UploadFile $uploadFile;

    const TENANTS_DIR = '/tenants';
    const REPORTS_TEMP_DIR = '/reports/temp';
    const REPORTS_DIR = '/reports';
    const STORAGE_BASE_PATH = '/var/www/backend/html/storage';
    const S3_PATH_MONTHLY_RECEIPTS_REPORTS = 'reports/financial/exits/monthly_receipts';

    const MONTHLY_RECEIPTS_BLADE_VIEW = 'reports/exits/monthlyReceipts/monthly_receipts';
    const MONTHLY_RECEIPTS_REPORT_NAME = 'monthly_receipts.pdf';




    public function __construct(
        GetExitsAction $getExitsAction,
        UpdateStatusExitsReportRequestsAction $updateStatusExitsReportRequestsAction,
        UpdateLinkExitsReportRequestsAction $updateLinkExitsReportRequestsAction,
        UpdateAmountsExitsReportRequestsAction $updateAmountsExitsReportRequestsAction,
        GetChurchAction $getChurchAction,
        UploadFile $uploadFile
    )
    {
        $this->getExitsAction = $getExitsAction;
        $this->updateStatusExitsReportRequestsAction = $updateStatusExitsReportRequestsAction;
        $this->updateLinkExitsReportRequestsAction = $updateLinkExitsReportRequestsAction;
        $this->updateAmountsExitsReportRequestsAction = $updateAmountsExitsReportRequestsAction;
        $this->getChurchAction = $getChurchAction;
        $this->uploadFile = $uploadFile;
    }


    /**
     * @throws Throwable
     */
    public function execute(MonthlyExitsReportData $report, string $tenant): void
    {
        $dates = $report->dates;

        if(count($dates) > 0)
        {
            try
            {
                $churchData = $this->getChurchAction->execute($tenant);

                $arrPathReceiptsLocal = [];
                $exitTypesAmount = [
                    'paymentsAmount' => 0,
                    'transfersAmount' => 0,
                    'ministerialTransferAmount' => 0,
                    'contributionsAmount' => 0,
                ];

                $filters = [
                  'exitTypes'      => implode(',', $report->exitTypes),
                ];

                foreach ($dates as $date)
                {
                    $exits = $this->getExitsAction->execute($date, $filters, false);
                    $linkReceiptExits = [];

                    foreach ($exits as $exit){
                        $exitTypesAmount['paymentsAmount'] += $exit->exitType == ExitRepository::PAYMENTS_VALUE ? $exit->amount : 0;
                        $exitTypesAmount['transfersAmount'] += $exit->exitType == ExitRepository::TRANSFER_VALUE ? $exit->amount : 0;
                        $exitTypesAmount['ministerialTransferAmount'] += $exit->exitType == ExitRepository::MINISTERIAL_TRANSFER_VALUE ? $exit->amount : 0;
                        $exitTypesAmount['contributionsAmount'] += $exit->exitType == ExitRepository::CONTRIBUTIONS_VALUE ? $exit->amount : 0;

                        $linkReceiptExits[] = $exit->receiptLink;
                    }

                    if(count($linkReceiptExits) > 0)
                        $arrPathReceiptsLocal = array_merge(
                            $arrPathReceiptsLocal,
                            $this->downloadImage($linkReceiptExits, $tenant));
                }

                if(count($arrPathReceiptsLocal) > 0)
                {
                    $localPathExitsMonthlyReceiptsReport = $this->generateSinglePDF($tenant, $arrPathReceiptsLocal, $filters, $dates, $exitTypesAmount, $churchData);
                    $pathReportUploaded = $this->uploadFile->upload($localPathExitsMonthlyReceiptsReport, self::S3_PATH_MONTHLY_RECEIPTS_REPORTS, $tenant);
                    $this->updateLinkExitsReportRequestsAction->execute($report->id, $pathReportUploaded);

                    $totalAmount = $exitTypesAmount['paymentsAmount'] + $exitTypesAmount['transfersAmount'] + $exitTypesAmount['ministerialTransferAmount'] + $exitTypesAmount['contributionsAmount'];
                    $this->updateAmountsExitsReportRequestsAction->execute($report->id, $totalAmount);

                    $this->cleanReportTempDir(self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR);
                    $this->cleanReportTempDir(self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_DIR);

                    $this->updateStatusExitsReportRequestsAction->execute($report->id, MonthlyExitsReportsRepository::DONE_STATUS_VALUE);
                }
                else
                {
                    $this->updateStatusExitsReportRequestsAction->execute($report->id, MonthlyExitsReportsRepository::NO_DATA_STATUS_VALUE);
                }
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
            $this->updateStatusExitsReportRequestsAction->execute($report->id, MonthlyExitsReportsRepository::ERROR_STATUS_VALUE);
        }
    }



    /**
     * @param array $links
     * @param string $tenant
     * @return array
     */
    private function downloadImage(array $links, string $tenant): array
    {
        $arrPaths = [];
        $counter = 0;

        foreach ($links as $link)
        {
            $response = Http::get($link);
            $imageName = time() . '_' . $counter . '_' .  basename($link);
            $imagePath = self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR . '/' . $imageName;
            $directoryName = dirname($imagePath);

            if (!is_dir($directoryName))
                mkdir($directoryName, 0777, true);

            file_put_contents($imagePath, $response->body());
            $arrPaths [] = $imagePath;
            $counter++;
        }

        return $arrPaths;
    }


    /**
     * Combina os comprovantes em um único PDF.
     *
     * @param string $tenant
     * @param array $links
     * @param array $filters
     * @param array $dates
     * @param array $exitTypesAmount
     * @param object $churchData
     * @return string
     */
    private function generateSinglePDF(string $tenant, array $links, array $filters, array $dates, array $exitTypesAmount, object $churchData): string
    {
        $timestamp = date('YmdHis');
        $directoryPath = self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR;

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0775, true);
        }

        $pdfPath = $directoryPath . '/' . $timestamp . '_' . self::MONTHLY_RECEIPTS_REPORT_NAME;

        $html = view(self::MONTHLY_RECEIPTS_BLADE_VIEW, [
            'tenant' => $tenant,
            'links' => $links,
            'filters' => $filters,
            'dates' => $dates,
            'exitTypesAmount' => $exitTypesAmount,
            'churchData' => $churchData,
        ])->render();

        PDFGenerator::save($html, $pdfPath);

        return $pdfPath;
    }




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
}
