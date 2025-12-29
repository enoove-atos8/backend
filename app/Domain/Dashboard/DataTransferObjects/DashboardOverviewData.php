<?php

namespace App\Domain\Dashboard\DataTransferObjects;

class DashboardOverviewData
{
    public function __construct(
        public int $month,
        public int $year,
        public string $label,
        public IndicatorData $members,
        public IndicatorData $tithes,
        public IndicatorData $purchases,
        public IndicatorData $exits
    ) {}
}
