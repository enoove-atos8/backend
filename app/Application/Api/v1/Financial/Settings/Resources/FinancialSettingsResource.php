<?php

namespace App\Application\Api\v1\Financial\Settings\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialSettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'budgetValue' => (float) ($this->budget_value ?? 0),
            'budgetType' => $this->budget_type,
            'budgetActivated' => (bool) $this->budget_activated,
        ];
    }
}
