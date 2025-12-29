<?php

namespace App\Domain\Dashboard\DataTransferObjects;

class TrendData
{
    public function __construct(
        public ?float $value,
        public ?float $percentage,
        public string $direction,
        public string $label
    ) {}

    public static function calculate(float $current, float $previous, bool $usePercentage = true): self
    {
        $difference = $current - $previous;
        $direction = $difference >= 0 ? 'up' : 'down';

        if ($usePercentage) {
            $percentage = $previous > 0 ? round(($difference / $previous) * 100, 1) : 0;
            $label = ($difference >= 0 ? '+' : '').$percentage.'%';

            return new self(
                value: null,
                percentage: $percentage,
                direction: $direction,
                label: $label
            );
        }

        $label = ($difference >= 0 ? '+' : '').(int) $difference;

        return new self(
            value: $difference,
            percentage: null,
            direction: $direction,
            label: $label
        );
    }
}
