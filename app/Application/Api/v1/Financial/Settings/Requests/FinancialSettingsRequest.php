<?php

namespace App\Application\Api\v1\Financial\Settings\Requests;

use App\Domain\Financial\Settings\DataTransferObjects\FinancialSettingsData;
use Illuminate\Foundation\Http\FormRequest;

class FinancialSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'budgetValue' => ['required', 'numeric', 'min:0'],
            'budgetType' => ['required', 'string', 'in:tithes,exits'],
            'budgetActivated' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'budgetValue.required' => 'O valor do orçamento é obrigatório',
            'budgetValue.numeric' => 'O valor do orçamento deve ser um valor numérico',
            'budgetValue.min' => 'O valor do orçamento deve ser maior ou igual a zero',
            'budgetType.required' => 'O tipo do orçamento é obrigatório',
            'budgetType.string' => 'O tipo do orçamento deve ser um texto',
            'budgetType.in' => 'O tipo do orçamento deve ser tithes ou exits',
            'budgetActivated.boolean' => 'O status de ativação deve ser verdadeiro ou falso',
        ];
    }

    public function toData(): FinancialSettingsData
    {
        return new FinancialSettingsData(
            budgetValue: (float) $this->input('budgetValue'),
            budgetType: $this->input('budgetType'),
            budgetActivated: $this->input('budgetActivated') !== null ? (bool) $this->input('budgetActivated') : true,
        );
    }
}
