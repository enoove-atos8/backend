<?php

namespace App\Infrastructure\Services\Atos8\Financial\Entries\Reports;

use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Entries\Reports\Models\ReportRequests;
use DateTime;
use Domain\Ecclesiastical\Groups\Actions\GetGroupsByIdAction;
use Domain\Financial\Entries\Reports\Actions\UpdateLinkReportRequestsAction;
use Domain\Financial\Entries\Reports\Actions\UpdateStatusReportRequestsAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Entries\Reports\ReportRequestsRepository;
use Infrastructure\Util\Storage\S3\UploadFile;
use Throwable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerateMonthlyReceiptsReport
{

    private array $linkReceiptList = [];
    private GetEntriesAction $getEntriesAction;

    private UpdateStatusReportRequestsAction $updateStatusReportRequestsAction;
    private UpdateLinkReportRequestsAction $updateLinkReportRequestsAction;
    private GetGroupsByIdAction $getGroupsByIdAction;

    private UploadFile $uploadFile;

    const STORAGE_BASE_PATH = '/var/www/backend/html/storage/';
    const PATH_ENTRIES_MONTHLY_RECEIPTS_REPORTS = 'entries/reports/monthly_receipts';




    public function __construct(
        GetEntriesAction $getEntriesAction,
        UpdateStatusReportRequestsAction $updateStatusReportRequestsAction,
        UpdateLinkReportRequestsAction $updateLinkReportRequestsAction,
        GetGroupsByIdAction $getGroupsByIdAction,
        UploadFile $uploadFile
    )
    {
        $this->getEntriesAction = $getEntriesAction;
        $this->updateStatusReportRequestsAction = $updateStatusReportRequestsAction;
        $this->updateLinkReportRequestsAction = $updateLinkReportRequestsAction;
        $this->getGroupsByIdAction = $getGroupsByIdAction;
        $this->uploadFile = $uploadFile;
    }


    /**
     * @throws Throwable
     */
    public function __invoke(ReportRequests $requests, string $tenant): void
    {
        $dates = $requests->dates;

        /*usort($dates, function($a, $b) {
            return strtotime($a) - strtotime($b);
        });*/

        if(count($dates) > 0)
        {
            $arrPathReceiptsLocal = [];
            $group = null;

            if(!is_null($requests->group_received_id))
                $group = $this->getGroupsByIdAction->__invoke($requests->group_received_id);

            $filters = [
              'entryTypes'      => implode(',', $requests->entry_types),
              'groupReceivedId' => $requests->group_received_id,
            ];



            foreach ($dates as $date)
            {
                $entries = $this->getEntriesAction->__invoke($date, $filters, false);
                $linkReceiptEntries = [];

                foreach ($entries as $entry)
                    $linkReceiptEntries [] = $entry->entries_receipt_link;

                if(count($linkReceiptEntries) > 0)
                    $arrPathReceiptsLocal = array_merge(
                        $arrPathReceiptsLocal,
                        $this->downloadImage($linkReceiptEntries, $tenant));
            }

            if(count($arrPathReceiptsLocal) > 0)
            {
                $localPathEntriesMonthlyReceiptsReport = $this->generateSinglePDF($tenant, $arrPathReceiptsLocal, $filters, $dates, $group);
                $pathReportUploaded = $this->uploadFile->upload($localPathEntriesMonthlyReceiptsReport, self::PATH_ENTRIES_MONTHLY_RECEIPTS_REPORTS, $tenant);
                $this->updateLinkReportRequestsAction->__invoke($requests->id, $pathReportUploaded);

                $this->cleanReportTempDir(self::STORAGE_BASE_PATH . 'tenants/' . $tenant . '/reports/temp/');

                $this->updateStatusReportRequestsAction->__invoke($requests->id, ReportRequestsRepository::DONE_STATUS_VALUE);
            }
        }
        else
        {
            $this->updateStatusReportRequestsAction->__invoke($requests->id, ReportRequestsRepository::ERROR_STATUS_VALUE);
            throw new GeneralExceptions('', 500);
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
            $imagePath = self::STORAGE_BASE_PATH . 'tenants/' . $tenant . '/reports/temp/' . $imageName;
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
     * @param mixed $group
     * @return string
     * @throws GeneralExceptions
     */
    private function generateSinglePDF(string $tenant, array $links, array $filters, array $dates, mixed $group): string
    {
        $timestamp = date('YmdHis');
        $directoryPath = self::STORAGE_BASE_PATH . 'tenants/' . $tenant . '/reports/temp/';

        $html = view('reports/entries/monthlyReceipts/monthly_receipts', [
            'tenant' => $tenant,
            'links' => $links,
            'filters' => $filters,
            'dates' => $dates,
            'group' => $group,
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdfPath = $directoryPath . $timestamp . '_monthly_receipts.pdf';

        $pdf->save($pdfPath);

        if ($pdf instanceof \Barryvdh\DomPDF\PDF) {
            return $pdfPath;
        } else {
            throw new GeneralExceptions('Houve um erro ao gerar o relatório, tente novamente mais tarde!', 500);
        }
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
