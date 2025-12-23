<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests;

use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestReceiptData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UpdateAmountRequestReceiptRequest extends FormRequest
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
            'amount' => 'sometimes|numeric|min:0.01',
            'description' => 'sometimes|string|min:3',
            'imageUrl' => 'sometimes|string|url',
            'receiptDate' => 'sometimes|date|before_or_equal:today',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'amount.numeric' => 'O valor do comprovante deve ser numérico!',
            'amount.min' => 'O valor do comprovante deve ser maior que zero!',
            'description.min' => 'A descrição deve ter pelo menos 3 caracteres!',
            'imageUrl.url' => 'A URL da imagem deve ser válida!',
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
            imageUrl: $this->input('imageUrl'),
            receiptDate: $this->input('receiptDate'),
        );
    }
}
