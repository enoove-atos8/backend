<?php

namespace App\Infrastructure\Services\Atos8\Financial\Entries\Reports;

use App\Domain\Ecclesiastical\Groups\Groups\Actions\GetGroupsByIdAction;
use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Reports\Entries\Actions\UpdateAmountsReportRequestsAction;
use App\Domain\Financial\Reports\Entries\Actions\UpdateLinkReportRequestsAction;
use App\Domain\Financial\Reports\Entries\Actions\UpdateStatusReportRequestsAction;
use App\Domain\Financial\Reports\Entries\DataTransferObjects\MonthlyReportData;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use App\Infrastructure\Repositories\Financial\Reports\Entries\MonthlyReportsRepository;
use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Exception;
use Illuminate\Support\Facades\Http;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class GenerateMonthlyReceiptsReport
{
    private array $linkReceiptList = [];

    private GetEntriesAction $getEntriesAction;

    private UpdateStatusReportRequestsAction $updateStatusReportRequestsAction;

    private UpdateLinkReportRequestsAction $updateLinkReportRequestsAction;

    private UpdateAmountsReportRequestsAction $updateAmountsReportRequestsAction;

    private GetGroupsByIdAction $getGroupsByIdAction;

    private GetChurchAction $getChurchAction;

    private UploadFile $uploadFile;

    const TENANTS_DIR = '/tenants';

    const REPORTS_TEMP_DIR = '/reports/temp';

    const REPORTS_DIR = '/reports';

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage';

    const S3_PATH_MONTHLY_RECEIPTS_REPORTS = 'reports/financial/entries/monthly_receipts';

    const PIX = 'pix';

    const CASH = 'cash';

    const MONTHLY_RECEIPTS_BLADE_VIEW = 'reports/entries/monthlyReceipts/monthly_receipts';

    const MONTHLY_RECEIPTS_REPORT_NAME = 'monthly_receipts.pdf';

    public function __construct(
        GetEntriesAction $getEntriesAction,
        UpdateStatusReportRequestsAction $updateStatusReportRequestsAction,
        UpdateLinkReportRequestsAction $updateLinkReportRequestsAction,
        GetGroupsByIdAction $getGroupsByIdAction,
        UpdateAmountsReportRequestsAction $updateAmountsReportRequestsAction,
        GetChurchAction $getChurchAction,
        UploadFile $uploadFile
    ) {
        $this->getEntriesAction = $getEntriesAction;
        $this->updateStatusReportRequestsAction = $updateStatusReportRequestsAction;
        $this->updateLinkReportRequestsAction = $updateLinkReportRequestsAction;
        $this->getGroupsByIdAction = $getGroupsByIdAction;
        $this->updateAmountsReportRequestsAction = $updateAmountsReportRequestsAction;
        $this->getChurchAction = $getChurchAction;
        $this->uploadFile = $uploadFile;
    }

    /**
     * @throws Throwable
     */
    public function execute(MonthlyReportData $report, string $tenant): void
    {
        $dates = $report->dates;

        if (count($dates) > 0) {
            try {
                $receiptsData = [];
                $group = null;
                $entryTypesAmount = [
                    'titheAmount' => 0,
                    'designatedAmount' => 0,
                    'offerAmount' => 0,
                ];

                if (! is_null($report->group->id)) {
                    $group = $this->getGroupsByIdAction->execute($report->group->id);
                }

                $filters = [
                    'entryTypes' => implode(',', $report->entryTypes),
                    'groupReceivedId' => $report->group->id,
                    'transactionType' => $report->includeCashDeposit == 1 ? self::PIX.','.self::CASH : self::PIX,
                ];

                $cashCultReceipts = [];

                foreach ($dates as $date) {
                    $entries = $this->getEntriesAction->execute($date, $filters, false);

                    foreach ($entries as $entry) {
                        $entryTypesAmount['titheAmount'] += $entry->entries_entry_type == EntryRepository::TITHE_VALUE ? $entry->entries_amount : 0;
                        $entryTypesAmount['designatedAmount'] += $entry->entries_entry_type == EntryRepository::DESIGNATED_VALUE ? $entry->entries_amount : 0;
                        $entryTypesAmount['offerAmount'] += $entry->entries_entry_type == EntryRepository::OFFER_VALUE ? $entry->entries_amount : 0;

                        if ($report->includeCashDeposit == 1) {
                            if ($entry->entries_transaction_type == self::CASH) {
                                $cultId = $entry->entries_cult_id ?? 'no_cult';
                                if (! isset($cashCultReceipts[$cultId])) {
                                    $cashCultReceipts[$cultId] = true;
                                    $receiptsData[] = [
                                        'link' => $entry->entries_receipt_link,
                                        'transactionType' => self::CASH,
                                        'entryType' => $entry->entries_entry_type,
                                        'amount' => $entry->entries_amount,
                                        'dateCompensation' => $entry->entries_date_transaction_compensation ?? null,
                                    ];
                                } else {
                                    // Para entradas cash do mesmo culto, apenas adicionar o valor ao registro existente
                                    foreach ($receiptsData as &$receiptData) {
                                        if ($receiptData['link'] === $entry->entries_receipt_link && $receiptData['transactionType'] === self::CASH) {
                                            $receiptData['amount'] += $entry->entries_amount;
                                            break;
                                        }
                                    }
                                    unset($receiptData);
                                }
                            } else {
                                $receiptsData[] = [
                                    'link' => $entry->entries_receipt_link,
                                    'transactionType' => self::PIX,
                                    'entryType' => $entry->entries_entry_type,
                                    'amount' => $entry->entries_amount,
                                    'dateCompensation' => $entry->entries_date_transaction_compensation ?? null,
                                ];
                            }
                        } elseif ($report->includeCashDeposit == 0) {
                            if ($entry->entries_transaction_type == self::PIX) {
                                $receiptsData[] = [
                                    'link' => $entry->entries_receipt_link,
                                    'transactionType' => self::PIX,
                                    'entryType' => $entry->entries_entry_type,
                                    'amount' => $entry->entries_amount,
                                    'dateCompensation' => $entry->entries_date_transaction_compensation ?? null,
                                ];
                            }
                        }
                    }
                }

                if (count($receiptsData) > 0) {
                    // Baixar as imagens e adicionar o path local
                    $receiptsData = $this->downloadImagesWithData($receiptsData, $tenant);

                    $localPathEntriesMonthlyReceiptsReport = $this->generateSinglePDF($tenant, $receiptsData, $filters, $dates, $group, $entryTypesAmount);
                    $pathReportUploaded = $this->uploadFile->upload($localPathEntriesMonthlyReceiptsReport, self::S3_PATH_MONTHLY_RECEIPTS_REPORTS, $tenant);
                    $this->updateLinkReportRequestsAction->execute($report->id, $pathReportUploaded);
                    $this->updateAmountsReportRequestsAction->execute($report->id, $entryTypesAmount);

                    $this->cleanReportTempDir(self::STORAGE_BASE_PATH.self::TENANTS_DIR.'/'.$tenant.self::REPORTS_TEMP_DIR);
                    $this->cleanReportTempDir(self::STORAGE_BASE_PATH.self::TENANTS_DIR.'/'.$tenant.self::REPORTS_DIR);

                    $this->updateStatusReportRequestsAction->execute($report->id, MonthlyReportsRepository::DONE_STATUS_VALUE);
                } else {
                    $this->updateStatusReportRequestsAction->execute($report->id, MonthlyReportsRepository::NO_RECEIPTS_STATUS_VALUE);
                }
            } catch (Exception $e) {
                throw new GeneralExceptions(
                    'Houve um erro ao gerar o relatório: '.$e->getMessage(),
                    500
                );
            }
        } else {
            $this->updateStatusReportRequestsAction->execute($report->id, MonthlyReportsRepository::ERROR_STATUS_VALUE);
        }
    }

    private function downloadImage(array $links, string $tenant): array
    {
        $arrPaths = [];
        $counter = 0;

        foreach ($links as $link) {
            $response = Http::get($link);
            $imageName = time().'_'.$counter.'_'.basename($link);
            $imagePath = self::STORAGE_BASE_PATH.self::TENANTS_DIR.'/'.$tenant.self::REPORTS_TEMP_DIR.'/'.$imageName;
            $directoryName = dirname($imagePath);

            if (! is_dir($directoryName)) {
                mkdir($directoryName, 0777, true);
            }

            file_put_contents($imagePath, $response->body());
            $arrPaths[] = $imagePath;
            $counter++;
        }

        return $arrPaths;
    }

    /**
     * Baixa as imagens dos comprovantes e adiciona o path local aos dados.
     */
    private function downloadImagesWithData(array $receiptsData, string $tenant): array
    {
        $counter = 0;

        foreach ($receiptsData as &$receipt) {
            if (! empty($receipt['link'])) {
                $response = Http::get($receipt['link']);
                $imageName = time().'_'.$counter.'_'.basename($receipt['link']);
                $imagePath = self::STORAGE_BASE_PATH.self::TENANTS_DIR.'/'.$tenant.self::REPORTS_TEMP_DIR.'/'.$imageName;
                $directoryName = dirname($imagePath);

                if (! is_dir($directoryName)) {
                    mkdir($directoryName, 0777, true);
                }

                file_put_contents($imagePath, $response->body());
                $receipt['localPath'] = $imagePath;
                $counter++;
            }
        }
        unset($receipt);

        return $receiptsData;
    }

    /**
     * Combina os comprovantes em um único PDF.
     *
     * @throws Throwable
     */
    private function generateSinglePDF(string $tenant, array $links, array $filters, array $dates, mixed $group, array $entryTypesAmount): string
    {
        $timestamp = date('YmdHis');
        $directoryPath = self::STORAGE_BASE_PATH.self::TENANTS_DIR.'/'.$tenant.self::REPORTS_TEMP_DIR;

        if (! file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        $pdfPath = $directoryPath.'/'.$timestamp.'_'.self::MONTHLY_RECEIPTS_REPORT_NAME;

        // Buscar dados da igreja
        $churchData = $this->getChurchAction->execute($tenant);

        // Criar objeto reportData com as informações necessárias
        $reportData = (object) [
            'churchData' => $churchData,
        ];

        $html = view(self::MONTHLY_RECEIPTS_BLADE_VIEW, [
            'reportData' => $reportData,
            'tenant' => $tenant,
            'links' => $links,
            'filters' => $filters,
            'dates' => $dates,
            'group' => $group,
            'entryTypesAmount' => $entryTypesAmount,
        ])->render();

        PdfGenerator::save($html, $pdfPath);

        return $pdfPath;
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
        rmdir($dir); // Remove o diretório após esvaziá-lo
    }
}
