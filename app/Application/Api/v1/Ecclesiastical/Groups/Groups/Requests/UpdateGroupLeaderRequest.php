<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupLeaderRequest extends FormRequest
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
            'leader_id' => ['nullable', 'integer', 'exists:members,id'],
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
            'leader_id.integer' => 'O ID do líder deve ser um número inteiro.',
            'leader_id.exists' => 'O membro informado não existe.',
        ];
    }
}
