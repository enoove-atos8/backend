<?php

namespace Application\Api\v1\Financial\Entries\Entries\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiptEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'receipt' => 'required|max:2048',
        ];
    }


    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'receipt.required'   => 'É necessário anexar o comprovante da transação antes de prosseguir!',
            'receipt.max'        => 'O tamanho máximo do avatar deve ser de até 2MB!',
        ];
    }
}
