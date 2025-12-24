<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests;

use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReceiptData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AmountRequestReceiptRequest extends FormRequest
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
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|min:3',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'receiptDate' => 'required|date|before_or_equal:today',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'O valor do comprovante é obrigatório!',
            'amount.numeric' => 'O valor do comprovante deve ser numérico!',
            'amount.min' => 'O valor do comprovante deve ser maior que zero!',
            'description.required' => 'A descrição do comprovante é obrigatória!',
            'description.min' => 'A descrição deve ter pelo menos 3 caracteres!',
            'file.required' => 'O arquivo do comprovante é obrigatório!',
            'file.file' => 'O arquivo enviado é inválido!',
            'file.mimes' => 'O arquivo deve ser PDF, JPG, JPEG ou PNG!',
            'file.max' => 'O arquivo não pode exceder 10MB!',
            'receiptDate.required' => 'A data do comprovante é obrigatória!',
            'receiptDate.date' => 'A data do comprovante deve ser uma data válida!',
            'receiptDate.before_or_equal' => 'A data do comprovante não pode ser futura!',
        ];
    }

    /**
     * Function to data transfer objects to AmountRequestReceiptData class
     *
     * @throws UnknownProperties
     */
    public function receiptData(int $amountRequestId): AmountRequestReceiptData
    {
        return new AmountRequestReceiptData(
            amountRequestId: $amountRequestId,
            amount: $this->input('amount'),
            description: $this->input('description'),
            imageUrl: null,
            receiptDate: $this->input('receiptDate'),
            createdBy: $this->user()->id,
        );
    }
}
