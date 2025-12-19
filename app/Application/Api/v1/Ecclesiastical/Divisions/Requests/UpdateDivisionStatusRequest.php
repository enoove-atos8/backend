<?php

namespace Application\Api\v1\Ecclesiastical\Divisions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDivisionStatusRequest extends FormRequest
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
            'enabled' => 'required|boolean',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'enabled.required' => 'O campo enabled é obrigatório!',
            'enabled.boolean' => 'O campo enabled deve ser verdadeiro ou falso!',
        ];
    }
}
