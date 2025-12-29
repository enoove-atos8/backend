<?php

namespace Application\Api\v1\Financial\Entries\Entries\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntriesEvolutionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'limit' => 'required|integer|in:6,12',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'limit.required' => "O parâmetro 'limit' é obrigatório!",
            'limit.integer' => "O parâmetro 'limit' deve ser um número inteiro!",
            'limit.in' => "O parâmetro 'limit' deve ser 6 ou 12!",
        ];
    }

    /**
     * Get the validated limit value
     */
    public function getLimit(): int
    {
        return (int) $this->input('limit');
    }
}
