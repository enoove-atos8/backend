<?php

namespace Application\Api\v1\Secretary\Membership\Membership\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddMembersToGroupRequest extends FormRequest
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
            'groupId' => 'required|integer',
            'memberIds' => 'required|array|min:1',
            'memberIds.*' => 'required|integer|exists:members,id',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'groupId.required' => 'O ID do grupo é obrigatório',
            'groupId.integer' => 'O ID do grupo deve ser um número inteiro',
            'memberIds.required' => 'A lista de membros é obrigatória',
            'memberIds.array' => 'A lista de membros deve ser um array',
            'memberIds.min' => 'Deve haver pelo menos um membro na lista',
            'memberIds.*.integer' => 'Cada ID de membro deve ser um número inteiro',
            'memberIds.*.exists' => 'Um ou mais membros não existem',
        ];
    }
}
