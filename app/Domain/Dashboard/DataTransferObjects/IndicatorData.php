<?php

namespace App\Domain\Dashboard\DataTransferObjects;

class IndicatorData
{
    public function __construct(
        public float $total,
        public TrendData $trend
    ) {}
}
