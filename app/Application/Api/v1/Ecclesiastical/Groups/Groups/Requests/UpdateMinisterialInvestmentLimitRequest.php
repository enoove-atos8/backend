<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMinisterialInvestmentLimitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'ministerial_investment_limit' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ministerial_investment_limit.numeric' => 'O limite deve ser um valor numérico!',
            'ministerial_investment_limit.min' => 'O limite não pode ser negativo!',
        ];
    }
}
