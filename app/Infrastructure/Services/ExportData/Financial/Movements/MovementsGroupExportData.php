<?php

namespace Infrastructure\Services\ExportData\Financial\Movements;

use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Infrastructure\Exceptions\GeneralExceptions;

class MovementsGroupExportData
{
    private int $groupId;
    private string $dates;
    private string $type;

    private const ALLOWED_TYPES = ['PDF', 'XLSX'];
    private const STORAGE_BASE_PATH = '/var/www/backend/html/storage';
    private const TEMP_DIR = '/temp';

    /**
     * @throws GeneralExceptions
     */
    public function __construct(int $groupId, string $dates, string $type)
    {
        $this->groupId = $groupId;
        $this->dates = $dates;
        $this->type = strtoupper($type);

        if (!in_array($this->type, self::ALLOWED_TYPES)) {
            throw new GeneralExceptions('Invalid export type. Allowed types are: ' . implode(', ', self::ALLOWED_TYPES));
        }
    }

    /**
     * @throws GeneralExceptions
     */
    public function export(array $data, string $groupName, $church = null): mixed
    {
        return match ($this->type) {
            'PDF' => $this->exportToPdf($data, $groupName, $church),
            'XLSX' => $this->exportToXlsx($data, $groupName),
            default => throw new GeneralExceptions('Unsupported export type'),
        };
    }

    /**
     * @param array $data
     * @param string $groupName
     * @param $church
     * @return string
     */
    private function exportToPdf(array $data, string $groupName, $church = null): string
    {
        $html = view('reports.financial.movements.movements-group', [
            'data' => $data,
            'groupId' => $this->groupId,
            'groupName' => $groupName,
            'dates' => $this->dates,
            'church' => $church,
        ])->render();

        $timestamp = date('YmdHis');
        $directoryPath = self::STORAGE_BASE_PATH . self::TEMP_DIR;

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0775, true);
        }

        $pdfPath = $directoryPath . '/' . $timestamp . '_movements_group.pdf';

        PDFGenerator::save($html, $pdfPath);

        $pdfContent = file_get_contents($pdfPath);
        unlink($pdfPath);

        return $pdfContent;
    }

    /**
     * @throws GeneralExceptions
     */
    private function exportToXlsx(array $data, string $groupName): string
    {
        // To be implemented in the future
        throw new GeneralExceptions('XLSX export not implemented yet');
    }
}
