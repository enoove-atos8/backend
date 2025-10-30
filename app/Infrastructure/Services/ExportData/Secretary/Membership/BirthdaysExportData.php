<?php

namespace Infrastructure\Services\ExportData\Secretary\Membership;

use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Infrastructure\Exceptions\GeneralExceptions;

class BirthdaysExportData
{
    private string $month;
    private string $type;
    private array $fields;

    private const ALLOWED_TYPES = ['PDF', 'XLSX'];
    private const STORAGE_BASE_PATH = '/var/www/backend/html/storage';
    private const TEMP_DIR = '/temp';

    public function __construct(string $month, string $type, string $fields)
    {
        $this->month = $month;
        $this->type = strtoupper($type);
        $this->fields = array_map('trim', explode(',', $fields));

        if (!in_array($this->type, self::ALLOWED_TYPES)) {
            throw new GeneralExceptions('Invalid export type. Allowed types are: ' . implode(', ', self::ALLOWED_TYPES));
        }
    }

    public function export(array $data): mixed
    {
        return match ($this->type) {
            'PDF' => $this->exportToPdf($data),
            'XLSX' => $this->exportToXlsx($data),
            default => throw new GeneralExceptions('Unsupported export type'),
        };
    }

    private function mapFieldsToCamelCase(array $fields): array
    {
        return array_map(function ($field) {
            if($field !== 'age'){
                return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $field))));
            }
        }, $fields);
    }

    private function exportToPdf(array $data): string
    {
        $mappedFields = $this->mapFieldsToCamelCase($this->fields);

        $html = view('reports.secretary.membership.birthdays', [
            'data' => $data,
            'month' => $this->month,
            'fields' => $mappedFields
        ])->render();

        $timestamp = date('YmdHis');
        $directoryPath = self::STORAGE_BASE_PATH . self::TEMP_DIR;

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0775, true);
        }

        $pdfPath = $directoryPath . '/' . $timestamp . '_birthdays.pdf';

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
