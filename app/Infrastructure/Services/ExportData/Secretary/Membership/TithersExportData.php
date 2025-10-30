<?php

namespace App\Infrastructure\Services\ExportData\Secretary\Membership;

use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Carbon\Carbon;
use Infrastructure\Exceptions\GeneralExceptions;

class TithersExportData
{
    private string $month;
    private string $type;
    private float $monthlyTarget;
    private float $totalTithes;

    private const ALLOWED_TYPES = ['PDF', 'XLSX'];
    private const STORAGE_BASE_PATH = '/var/www/backend/html/storage';
    private const TEMP_DIR = '/temp';

    /**
     * @throws GeneralExceptions
     */
    public function __construct(string $month, string $type, mixed $monthlyTarget, float $totalTithes)
    {
        $this->month = $month;
        $this->type = strtoupper($type);
        $this->monthlyTarget = $monthlyTarget;
        $this->totalTithes = $totalTithes;

        if (!in_array($this->type, self::ALLOWED_TYPES)) {
            throw new GeneralExceptions('Invalid export type. Allowed types are: ' . implode(', ', self::ALLOWED_TYPES));
        }
    }




    /**
     * @throws GeneralExceptions
     */
    public function export(array $data): string
    {
        return match ($this->type) {
            'PDF' => $this->exportToPdf($data),
            'XLSX' => $this->exportToXlsx($data),
            default => throw new GeneralExceptions('Unsupported export type'),
        };
    }

    private function exportToPdf(array $data): string
    {
        $data = $data['data'] ?? $data;

        $html = view('reports.secretary.membership.tithers', [
            'data' => $data,
            'month' => Carbon::createFromFormat('Y-m', $this->month)->translatedFormat('F'),
            'totalTithes' => $this->totalTithes,
            'qtdTithers' => count($data),
            'monthlyTarget' => $this->monthlyTarget,
        ])->render();

        $timestamp = date('YmdHis');
        $directoryPath = self::STORAGE_BASE_PATH . self::TEMP_DIR;

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0775, true);
        }

        $pdfPath = $directoryPath . '/' . $timestamp . '_tithers.pdf';

        PDFGenerator::save($html, $pdfPath);

        $pdfContent = file_get_contents($pdfPath);
        unlink($pdfPath);

        return $pdfContent;
    }


    private function exportToXlsx(array $data): string
    {
        // To be implemented in the future
        throw new GeneralExceptions('XLSX export not implemented yet');
    }
}
