<?php

namespace App\Infrastructure\Services\Atos8\Financial\Purchases\Reports;

use App\Domain\Financial\Reports\Purchases\Actions\UpdateAmountsPurchasesReportRequestsAction;
use App\Domain\Financial\Reports\Purchases\Actions\UpdateLinkPurchasesReportRequestsAction;
use App\Domain\Financial\Reports\Purchases\Actions\UpdateStatusPurchasesReportRequestsAction;
use App\Domain\Financial\Reports\Purchases\DataTransferObjects\MonthlyPurchasesReportData;
use App\Infrastructure\Repositories\Financial\Reports\Purchases\MonthlyPurchasesReportsRepository;
use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Carbon\Carbon;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardByIdAction;
use Domain\Financial\Exits\Purchases\Actions\GetPurchasesAction;
use Exception;
use Illuminate\Support\Facades\Http;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class GenerateMonthlyReceiptsPurchaseReport
{
    private GetPurchasesAction $getPurchasesAction;

    private GetCardByIdAction $getCardByIdAction;

    private UpdateStatusPurchasesReportRequestsAction $updateStatusPurchasesReportRequestsAction;

    private UpdateLinkPurchasesReportRequestsAction $updateLinkPurchasesReportRequestsAction;

    private UpdateAmountsPurchasesReportRequestsAction $updateAmountsPurchasesReportRequestsAction;

    private GetChurchAction $getChurchAction;

    private UploadFile $uploadFile;

    const TENANTS_DIR = '/tenants';

    const REPORTS_TEMP_DIR = '/reports/temp';

    const REPORTS_DIR = '/reports';

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage';

    const S3_PATH_MONTHLY_RECEIPTS_PURCHASE_REPORTS = 'reports/financial/purchases/monthly_receipts_purchase';

    const MONTHLY_RECEIPTS_PURCHASE_BLADE_VIEW = 'reports/purchases/monthlyReceiptsPurchase/monthly_receipts_purchase';

    const MONTHLY_RECEIPTS_PURCHASE_REPORT_NAME = 'monthly_receipts_purchase.pdf';

    public function __construct(
        GetPurchasesAction $getPurchasesAction,
        GetCardByIdAction $getCardByIdAction,
        UpdateStatusPurchasesReportRequestsAction $updateStatusPurchasesReportRequestsAction,
        UpdateLinkPurchasesReportRequestsAction $updateLinkPurchasesReportRequestsAction,
        UpdateAmountsPurchasesReportRequestsAction $updateAmountsPurchasesReportRequestsAction,
        GetChurchAction $getChurchAction,
        UploadFile $uploadFile
    ) {
        $this->getPurchasesAction = $getPurchasesAction;
        $this->getCardByIdAction = $getCardByIdAction;
        $this->updateStatusPurchasesReportRequestsAction = $updateStatusPurchasesReportRequestsAction;
        $this->updateLinkPurchasesReportRequestsAction = $updateLinkPurchasesReportRequestsAction;
        $this->updateAmountsPurchasesReportRequestsAction = $updateAmountsPurchasesReportRequestsAction;
        $this->getChurchAction = $getChurchAction;
        $this->uploadFile = $uploadFile;
    }

    /**
     * @throws Throwable
     */
    public function execute(MonthlyPurchasesReportData $report, string $tenant): void
    {
        $dates = $report->dates;

        if (count($dates) > 0 && $report->cardId) {
            try {
                $churchData = $this->getChurchAction->execute($tenant);
                $card = $this->getCardByIdAction->execute($report->cardId);

                $cardReceiptLinks = [];
                $totalAmount = 0;

                foreach ($dates as $date) {
                    try {
                        $purchases = $this->getPurchasesAction->execute($card->id, $date);

                        if ($purchases && $purchases->count() > 0) {
                            foreach ($purchases as $purchase) {
                                $totalAmount += $purchase->amount;

                                if ($purchase->receipt) {
                                    $cardReceiptLinks[] = $purchase->receipt;
                                }
                            }
                        }
                    } catch (GeneralExceptions $e) {
                        if ($e->getCode() !== 404) {
                            throw $e;
                        }
                    }
                }

                if (count($cardReceiptLinks) > 0) {
                    $receiptPaths = $this->downloadImage($cardReceiptLinks, $tenant);

                    // Formatar período com mês capitalizado
                    $formattedPeriod = $this->formatPeriod($dates);

                    $localPathReceiptsReport = $this->generateSinglePDF(
                        $tenant,
                        $card,
                        $receiptPaths,
                        $dates,
                        $formattedPeriod,
                        $totalAmount,
                        $churchData
                    );
                    $pathReportUploaded = $this->uploadFile->upload(
                        $localPathReceiptsReport,
                        self::S3_PATH_MONTHLY_RECEIPTS_PURCHASE_REPORTS,
                        $tenant
                    );
                    $this->updateLinkPurchasesReportRequestsAction->execute($report->id, $pathReportUploaded);

                    $this->updateAmountsPurchasesReportRequestsAction->execute($report->id, $totalAmount);

                    $this->cleanReportTempDir(self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR);
                    $this->cleanReportTempDir(self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_DIR);

                    $this->updateStatusPurchasesReportRequestsAction->execute(
                        $report->id,
                        MonthlyPurchasesReportsRepository::DONE_STATUS_VALUE
                    );
                } else {
                    $this->updateStatusPurchasesReportRequestsAction->execute(
                        $report->id,
                        MonthlyPurchasesReportsRepository::NO_DATA_STATUS_VALUE
                    );
                }
            } catch (Exception $e) {
                throw new GeneralExceptions(
                    'Houve um erro ao gerar o relatório: ' . $e->getMessage(),
                    500
                );
            }
        } else {
            $this->updateStatusPurchasesReportRequestsAction->execute(
                $report->id,
                MonthlyPurchasesReportsRepository::ERROR_STATUS_VALUE
            );
        }
    }

    /**
     * Formata o período com mês capitalizado (ex: Outubro/2025)
     */
    private function formatPeriod(array $dates): string
    {
        $formattedDates = array_map(function ($date) {
            return Carbon::parse($date . '-01')->locale('pt_BR')->isoFormat('MMMM/YYYY');
        }, $dates);

        // Capitalizar primeira letra de cada mês
        $formattedDates = array_map(function ($date) {
            return ucfirst($date);
        }, $formattedDates);

        return implode(', ', $formattedDates);
    }

    private function downloadImage(array $links, string $tenant): array
    {
        $arrPaths = [];
        $counter = 0;

        foreach ($links as $link) {
            if (empty($link)) {
                continue;
            }

            $response = Http::get($link);
            $imageName = time() . '_' . $counter . '_' . basename($link);
            $imagePath = self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR . '/' . $imageName;
            $directoryName = dirname($imagePath);

            if (!is_dir($directoryName)) {
                mkdir($directoryName, 0777, true);
            }

            file_put_contents($imagePath, $response->body());
            $arrPaths[] = $imagePath;
            $counter++;
        }

        return $arrPaths;
    }

    /**
     * Combina os comprovantes em um único PDF.
     */
    private function generateSinglePDF(string $tenant, object $card, array $receiptPaths, array $dates, string $formattedPeriod, float $totalAmount, object $churchData): string
    {
        $timestamp = date('YmdHis');
        $directoryPath = self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR;

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        $pdfPath = $directoryPath . '/' . $timestamp . '_' . self::MONTHLY_RECEIPTS_PURCHASE_REPORT_NAME;

        $reportData = (object) [
            'churchData' => $churchData,
            'cardData' => $card,
            'generalData' => (object) [
                'period' => $formattedPeriod,
                'generationDate' => date('d/m/Y'),
                'totalAmount' => $totalAmount,
            ],
        ];

        $html = view(self::MONTHLY_RECEIPTS_PURCHASE_BLADE_VIEW, [
            'reportData' => $reportData,
            'tenant' => $tenant,
            'receiptPaths' => $receiptPaths,
            'dates' => $dates,
            'totalAmount' => $totalAmount,
        ])->render();

        PDFGenerator::save($html, $pdfPath);

        return $pdfPath;
    }

    public function cleanReportTempDir(string $directory): void
    {
        if (is_dir($directory)) {
            $files = scandir($directory);

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $directory . DIRECTORY_SEPARATOR . $file;

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
        if (!is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($filePath)) {
                    $this->deleteDirectory($filePath);
                } else {
                    unlink($filePath);
                }
            }
        }
        rmdir($dir);
    }
}
