<?php

namespace App\Infrastructure\Services\ExportData\Secretary\Membership;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Services\External\minIO\MinioStorageService;

class TithersExportData
{
    private string $month;
    private string $type;
    private float $monthlyTarget;
    private float $totalTithes;

    private const ALLOWED_TYPES = ['PDF', 'XLSX'];

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
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $data = $data['data'] ?? $data;

        $html = view('reports.secretary.membership.tithers', [
            'data' => $data,
            'month' => Carbon::createFromFormat('Y-m', $this->month)->translatedFormat('F'),
            'totalTithes' => $this->totalTithes,
            'qtdTithers' => count($data),
            'monthlyTarget' => $this->monthlyTarget,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }


    private function exportToXlsx(array $data): string
    {
        // To be implemented in the future
        throw new GeneralExceptions('XLSX export not implemented yet');
    }
}
