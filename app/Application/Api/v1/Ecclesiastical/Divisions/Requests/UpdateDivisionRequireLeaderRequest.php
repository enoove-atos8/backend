<?php

namespace Application\Api\v1\Ecclesiastical\Divisions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDivisionRequireLeaderRequest extends FormRequest
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
            'requireLeader' => 'required|boolean',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'requireLeader.required' => 'O campo requireLeader é obrigatório!',
            'requireLeader.boolean' => 'O campo requireLeader deve ser verdadeiro ou falso!',
        ];
    }
}
