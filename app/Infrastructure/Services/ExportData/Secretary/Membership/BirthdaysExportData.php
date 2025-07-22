<?php

namespace Infrastructure\Services\ExportData\Secretary\Membership;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Services\External\minIO\MinioStorageService;

class BirthdaysExportData
{
    private string $month;
    private string $type;
    private array $fields;

    private const ALLOWED_TYPES = ['PDF', 'XLSX'];

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
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        $mappedFields = $this->mapFieldsToCamelCase($this->fields);

        $html = view('reports.secretary.membership.birthdays', [
            'data' => $data,
            'month' => $this->month,
            'fields' => $mappedFields
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        return $pdfContent;
    }


    private function exportToXlsx(array $data): string
    {
        // To be implemented in the future
        throw new GeneralExceptions('XLSX export not implemented yet');
    }
}
