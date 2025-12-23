<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkExitRequest extends FormRequest
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
            'transferExitId' => 'present|nullable|integer',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'transferExitId.present' => 'O campo transferExitId é obrigatório!',
            'transferExitId.integer' => 'O ID da saída deve ser um número válido!',
        ];
    }
}
