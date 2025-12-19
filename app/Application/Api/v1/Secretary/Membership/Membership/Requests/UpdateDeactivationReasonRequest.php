<?php

namespace Application\Api\v1\Secretary\Membership\Membership\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeactivationReasonRequest extends FormRequest
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
            'deactivation_reason' => [
                'nullable',
                Rule::in([
                    'death',
                    'church_transfer',
                    'voluntary_withdrawal',
                    'exclusion',
                    'lost_contact',
                    'prolonged_inactivity',
                    'denomination_change',
                    'relocation',
                ]),
            ],
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'deactivation_reason.in' => 'O motivo de desativação informado não é válido.',
        ];
    }
}
