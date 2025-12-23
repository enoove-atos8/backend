<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseAmountRequestRequest extends FormRequest
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
            'reviewerId' => 'required|integer',
            'transactionType' => 'nullable|string|in:pix,transfer,deposit,cash',
            'transactionCompensation' => 'nullable|string',
            'dateTransactionCompensation' => 'nullable|date',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'reviewerId.required' => 'O revisor financeiro é obrigatório!',
            'reviewerId.integer' => 'O revisor financeiro deve ser um número válido!',
            'transactionType.in' => 'O tipo de transação deve ser: pix, transfer, deposit ou cash!',
        ];
    }

    /**
     * Get entry data for the devolution
     */
    public function entryData(): array
    {
        return [
            'transaction_type' => $this->input('transactionType', 'cash'),
            'transaction_compensation' => $this->input('transactionCompensation', 'immediate'),
            'date_transaction_compensation' => $this->input('dateTransactionCompensation', date('Y-m-d')),
        ];
    }
}
