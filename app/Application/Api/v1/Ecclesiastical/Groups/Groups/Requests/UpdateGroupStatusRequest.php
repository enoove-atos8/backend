<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupStatusRequest extends FormRequest
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
            'enabled' => ['required', 'boolean'],
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
            'enabled.required' => 'O campo enabled é obrigatório.',
            'enabled.boolean' => 'O campo enabled deve ser verdadeiro ou falso.',
        ];
    }
}
