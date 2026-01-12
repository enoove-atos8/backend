<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\AmountRequests\Requests;

use Domain\Ecclesiastical\Groups\AmountRequests\DataTransferObjects\AmountRequestData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AmountRequestRequest extends FormRequest
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
            'memberId' => 'required|integer',
            'groupId' => 'required|integer',
            'type' => 'nullable|string|in:group_fund,ministerial_investment',
            'aboveLimit' => 'nullable|boolean',
            'requestedAmount' => 'required|numeric|min:0.01',
            'description' => 'required|string|min:10',
            'proofDeadline' => 'required|date|after:today',
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [
            'memberId.required' => 'O membro solicitante é obrigatório!',
            'memberId.integer' => 'O membro solicitante deve ser um número válido!',
            'groupId.required' => 'O grupo é obrigatório!',
            'groupId.integer' => 'O grupo deve ser um número válido!',
            'type.in' => 'Tipo de solicitação inválido! Valores aceitos: Verba de Grupo ou Investimento Ministerial.',
            'aboveLimit.boolean' => 'O campo aboveLimit deve ser verdadeiro ou falso!',
            'requestedAmount.required' => 'O valor solicitado é obrigatório!',
            'requestedAmount.numeric' => 'O valor solicitado deve ser numérico!',
            'requestedAmount.min' => 'O valor solicitado deve ser maior que zero!',
            'description.required' => 'A descrição/finalidade é obrigatória!',
            'description.min' => 'A descrição deve ter pelo menos 10 caracteres!',
            'proofDeadline.required' => 'O prazo de comprovação é obrigatório!',
            'proofDeadline.date' => 'O prazo de comprovação deve ser uma data válida!',
            'proofDeadline.after' => 'O prazo de comprovação deve ser uma data futura!',
        ];
    }

    /**
     * Function to data transfer objects to AmountRequestData class
     *
     * @throws UnknownProperties
     */
    public function amountRequestData(): AmountRequestData
    {
        return new AmountRequestData(
            memberId: $this->input('memberId'),
            groupId: $this->input('groupId'),
            type: $this->input('type', 'group_fund'),
            aboveLimit: (bool) $this->input('aboveLimit', false),
            requestedAmount: $this->input('requestedAmount'),
            description: $this->input('description'),
            proofDeadline: $this->input('proofDeadline'),
            requestedBy: $this->user()->id,
        );
    }
}
