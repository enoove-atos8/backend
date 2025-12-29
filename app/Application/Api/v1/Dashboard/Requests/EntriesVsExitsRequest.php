<?php

namespace App\Application\Api\v1\Dashboard\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntriesVsExitsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'months' => 'required|integer|min:3|max:12',
        ];
    }

    public function messages(): array
    {
        return [
            'months.required' => "O parâmetro 'months' é obrigatório!",
            'months.integer' => "O parâmetro 'months' deve ser um número inteiro!",
            'months.min' => "O parâmetro 'months' deve ser no mínimo 3!",
            'months.max' => "O parâmetro 'months' deve ser no máximo 12!",
        ];
    }

    public function getMonths(): int
    {
        return (int) $this->input('months');
    }
}
