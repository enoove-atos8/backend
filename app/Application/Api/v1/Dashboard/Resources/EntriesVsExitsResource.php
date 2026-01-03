<?php

namespace App\Application\Api\v1\Dashboard\Resources;

use App\Domain\Dashboard\DataTransferObjects\EntriesVsExitsData;
use Illuminate\Http\Resources\Json\JsonResource;

class EntriesVsExitsResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        /** @var EntriesVsExitsData $data */
        $data = $this->resource;

        return [
            'success' => true,
            'data' => [
                'period' => [
                    'months' => $data->months,
                    'startDate' => $data->startDate,
                    'endDate' => $data->endDate,
                ],
                'chart' => [
                    'categories' => $data->categories,
                    'series' => [
                        'entries' => $this->formatMoneyArray($data->entries),
                        'exits' => $this->formatMoneyArray($data->exits),
                    ],
                ],
                'summary' => [
                    'totalEntries' => round($data->totalEntries, 2),
                    'totalExits' => round($data->totalExits, 2),
                    'balance' => round($data->balance, 2),
                    'averageMonthlyBalance' => round($data->averageMonthlyBalance, 2),
                    'averageMonthlyEntries' => round($data->averageMonthlyEntries, 2),
                    'averageMonthlyExits' => round($data->averageMonthlyExits, 2),
                ],
            ],
        ];
    }

    private function formatMoneyArray(array $values): array
    {
        return array_map(fn ($value) => round((float) $value, 2), $values);
    }
}
