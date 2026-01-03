<?php

namespace App\Domain\Dashboard\DataTransferObjects;

class EntriesVsExitsData
{
    public function __construct(
        public int $months,
        public string $startDate,
        public string $endDate,
        public array $categories,
        public array $entries,
        public array $exits,
        public float $totalEntries,
        public float $totalExits,
        public float $balance,
        public float $averageMonthlyBalance,
        public float $averageMonthlyEntries,
        public float $averageMonthlyExits
    ) {}
}
