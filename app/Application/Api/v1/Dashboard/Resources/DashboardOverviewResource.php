<?php

namespace App\Application\Api\v1\Dashboard\Resources;

use App\Domain\Dashboard\DataTransferObjects\DashboardOverviewData;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardOverviewResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        /** @var DashboardOverviewData $data */
        $data = $this->resource;

        return [
            'success' => true,
            'data' => [
                'period' => [
                    'month' => $data->month,
                    'year' => $data->year,
                    'label' => $data->label,
                ],
                'members' => $this->formatIndicator($data->members, false),
                'tithes' => $this->formatIndicator($data->tithes),
                'purchases' => $this->formatIndicator($data->purchases),
                'exits' => $this->formatIndicator($data->exits),
            ],
        ];
    }

    private function formatIndicator($indicator, bool $isMoney = true): array
    {
        return [
            'total' => $isMoney ? round($indicator->total, 2) : $indicator->total,
            'trend' => [
                'value' => $indicator->trend->value,
                'percentage' => $indicator->trend->percentage,
                'direction' => $indicator->trend->direction,
                'label' => $indicator->trend->label,
            ],
        ];
    }
}
