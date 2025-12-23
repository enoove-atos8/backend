<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectAmountRequestRequest extends FormRequest
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
            'rejectionReason' => 'required|string|min:10',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'rejectionReason.required' => 'O motivo da rejeição é obrigatório!',
            'rejectionReason.min' => 'O motivo da rejeição deve ter pelo menos 10 caracteres!',
        ];
    }
}
