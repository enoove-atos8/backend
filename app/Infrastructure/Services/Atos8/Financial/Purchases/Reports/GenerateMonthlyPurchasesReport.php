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
use Domain\Financial\Exits\Purchases\Actions\GetInstallmentsAction;
use Exception;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;

class GenerateMonthlyPurchasesReport
{
    private GetInstallmentsAction $getInstallmentsAction;

    private GetCardByIdAction $getCardByIdAction;

    private UpdateStatusPurchasesReportRequestsAction $updateStatusPurchasesReportRequestsAction;

    private UpdateLinkPurchasesReportRequestsAction $updateLinkPurchasesReportRequestsAction;

    private UpdateAmountsPurchasesReportRequestsAction $updateAmountsPurchasesReportRequestsAction;

    private GetChurchAction $getChurchAction;

    private UploadFile $uploadFile;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage';

    const S3_PATH_MONTHLY_PURCHASES_REPORTS = 'reports/financial/purchases/monthly_purchases';

    const TENANTS_DIR = '/tenants';

    const REPORTS_TEMP_DIR = '/reports/temp';

    const MONTHLY_PURCHASES_BLADE_VIEW = 'reports/purchases/monthlyPurchases/monthly_purchases';

    const MONTHLY_PURCHASES_REPORT_NAME = 'monthly_purchases.pdf';

    public function __construct(
        GetInstallmentsAction $getInstallmentsAction,
        GetCardByIdAction $getCardByIdAction,
        UpdateStatusPurchasesReportRequestsAction $updateStatusPurchasesReportRequestsAction,
        UpdateLinkPurchasesReportRequestsAction $updateLinkPurchasesReportRequestsAction,
        UpdateAmountsPurchasesReportRequestsAction $updateAmountsPurchasesReportRequestsAction,
        GetChurchAction $getChurchAction,
        UploadFile $uploadFile
    ) {
        $this->getInstallmentsAction = $getInstallmentsAction;
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
            $timestamp = date('YmdHis');
            $directoryPath = self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR;

            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            $pdfPath = $directoryPath . '/' . $timestamp . '_' . self::MONTHLY_PURCHASES_REPORT_NAME;

            try {
                $churchData = $this->getChurchAction->execute($tenant);
                $card = $this->getCardByIdAction->execute($report->cardId);
                $allInstallments = collect();

                foreach ($dates as $date) {
                    try {
                        // Busca parcelas do mês com dados da compra, fatura e grupo
                        $installments = $this->getInstallmentsAction->execute($card->id, $date);
                        if ($installments && $installments->count() > 0) {
                            $allInstallments = $allInstallments->merge($installments);
                        }
                    } catch (GeneralExceptions $e) {
                        if ($e->getCode() !== 404) {
                            throw $e;
                        }
                    }
                }

                if ($allInstallments->isEmpty()) {
                    $this->updateStatusPurchasesReportRequestsAction->execute(
                        $report->id,
                        MonthlyPurchasesReportsRepository::NO_DATA_STATUS_VALUE
                    );
                    return;
                }

                // O total é a soma dos valores das parcelas (installmentAmount)
                $totalAmount = $allInstallments->sum('installmentAmount');

                // Agrupar parcelas por grupo (recebedor)
                $purchasesByGroup = $this->groupInstallmentsByGroup($allInstallments);

                // Formatar período com mês capitalizado
                $formattedPeriod = $this->formatPeriod($dates);

                $reportData = (object) [
                    'churchData' => $churchData,
                    'cardData' => $card,
                    'generalData' => (object) [
                        'period' => $formattedPeriod,
                        'generationDate' => Carbon::createFromFormat('Y-m-d H:i:s', $report->generationDate)->format('d/m/Y'),
                        'totalPurchases' => $totalAmount,
                    ],
                    'purchasesByGroup' => $purchasesByGroup,
                ];

                $viewData = [
                    'reportData' => $reportData,
                    'monthlyPurchasesReportObject' => $report,
                ];

                $view = view(self::MONTHLY_PURCHASES_BLADE_VIEW, $viewData)->render();
                PDFGenerator::save($view, $pdfPath);

                $linkReport = $this->uploadFile->upload($pdfPath, self::S3_PATH_MONTHLY_PURCHASES_REPORTS, $tenant);
                $this->updateLinkPurchasesReportRequestsAction->execute($report->id, $linkReport);

                $this->updateAmountsPurchasesReportRequestsAction->execute($report->id, $totalAmount);

                $this->cleanReportTempDir(self::STORAGE_BASE_PATH . self::TENANTS_DIR . '/' . $tenant . self::REPORTS_TEMP_DIR);

                $this->updateStatusPurchasesReportRequestsAction->execute(
                    $report->id,
                    MonthlyPurchasesReportsRepository::DONE_STATUS_VALUE
                );

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
     * Agrupa as parcelas por grupo (recebedor)
     * Cada parcela (CardInstallmentData) contém: cardPurchaseData, cardInvoiceData, groupData, installment
     */
    private function groupInstallmentsByGroup($installments): array
    {
        $grouped = [];

        foreach ($installments as $installment) {
            $groupId = $installment->groupData->id ?? 0;
            $groupName = $installment->groupData->name ?? 'Sem Grupo';

            if (!isset($grouped[$groupId])) {
                $grouped[$groupId] = [
                    'groupId' => $groupId,
                    'groupName' => $groupName,
                    'purchases' => [],
                    'total' => 0,
                    'quantity' => 0,
                ];
            }

            // Monta objeto com dados da compra + parcela atual
            $purchaseWithInstallment = (object) [
                'id' => $installment->cardPurchaseData->id,
                'date' => $installment->date,
                'establishmentName' => $installment->cardPurchaseData->establishmentName,
                'purchaseDescription' => $installment->cardPurchaseData->purchaseDescription,
                'amount' => $installment->cardPurchaseData->amount,
                'installments' => $installment->cardPurchaseData->installments,
                'installmentAmount' => $installment->installmentAmount,
                'currentInstallment' => $installment->installment,
                'invoiceId' => $installment->cardInvoiceData->id ?? null,
            ];

            $grouped[$groupId]['purchases'][] = $purchaseWithInstallment;
            $grouped[$groupId]['total'] += $installment->installmentAmount;
            $grouped[$groupId]['quantity']++;
        }

        // Converter para objetos
        foreach ($grouped as &$group) {
            $group = (object) $group;
        }

        return array_values($grouped);
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
