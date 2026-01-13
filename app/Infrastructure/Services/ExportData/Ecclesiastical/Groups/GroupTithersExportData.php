<?php

namespace Infrastructure\Services\ExportData\Ecclesiastical\Groups;

use App\Infrastructure\Services\PDFGenerator\PDFGenerator;
use Carbon\Carbon;
use Infrastructure\Exceptions\GeneralExceptions;

class GroupTithersExportData
{
    private int $groupId;

    private string $type;

    private const ALLOWED_TYPES = ['PDF', 'XLSX'];

    /**
     * @throws GeneralExceptions
     */
    public function __construct(int $groupId, string $type)
    {
        $this->groupId = $groupId;
        $this->type = strtoupper($type);

        if (! in_array($this->type, self::ALLOWED_TYPES)) {
            throw new GeneralExceptions('Invalid export type. Allowed types are: '.implode(', ', self::ALLOWED_TYPES));
        }
    }

    /**
     * @throws GeneralExceptions
     */
    public function export(array $data, string $groupName, $church, ?string $leaderName = null): string
    {
        return match ($this->type) {
            'PDF' => $this->exportToPdf($data, $groupName, $church, $leaderName),
            'XLSX' => $this->exportToXlsx($data, $groupName, $church),
            default => throw new GeneralExceptions('Unsupported export type'),
        };
    }

    private function exportToPdf(array $data, string $groupName, $church, ?string $leaderName = null): string
    {
        // Obter os últimos 6 meses (do mês anterior para trás)
        $months = [];
        for ($i = 1; $i <= 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $months[] = [
                'key' => $date->format('Y-m'),
                'label' => ucfirst($date->translatedFormat('M/Y')),
            ];
        }
        $months = array_reverse($months);

        $html = view('reports.ecclesiastical.groups.group-tithers', [
            'data' => $data,
            'groupName' => $groupName,
            'leaderName' => $leaderName,
            'church' => $church,
            'months' => $months,
            'totalMembers' => count($data),
        ])->render();

        $timestamp = date('YmdHis');

        // Extrai o tenant da request para construir o path correto
        $tenant = explode('.', request()->getHost())[0];
        $directoryPath = storage_path('tenant').'/'.$tenant.'/app/temp';

        if (! file_exists($directoryPath)) {
            mkdir($directoryPath, 0775, true);
        }

        $pdfPath = $directoryPath.'/'.$timestamp.'_group_tithers.pdf';

        PDFGenerator::save($html, $pdfPath);

        $pdfContent = file_get_contents($pdfPath);
        unlink($pdfPath);

        return $pdfContent;
    }

    private function exportToXlsx(array $data, string $groupName, $church): string
    {
        // To be implemented in the future
        throw new GeneralExceptions('XLSX export not implemented yet');
    }
}
