<?php

namespace App\Domain\Financial\Settings\DataTransferObjects;

use stdClass;

class FinancialSettingsData
{
    public function __construct(
        public ?int $id = null,
        public ?float $budgetValue = null,
        public ?string $budgetType = null,
        public bool $budgetActivated = true,
    ) {}

    public static function fromResponse(stdClass|array $data): self
    {
        if (is_array($data)) {
            $data = (object) $data;
        }

        return new self(
            id: $data->id ?? null,
            budgetValue: isset($data->budget_value) ? (float) $data->budget_value : null,
            budgetType: $data->budget_type ?? null,
            budgetActivated: (bool) ($data->budget_activated ?? true),
        );
    }
}
