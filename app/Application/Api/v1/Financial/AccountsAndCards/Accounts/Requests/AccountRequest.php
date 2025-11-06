<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Accounts\Requests;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => '',
            'accountType' => 'required',
            'bankName' => 'required',
            'agencyNumber' => 'required',
            'accountNumber' => 'required',
            'initialBalance' => 'required',
            'initialBalanceDate' => 'required',
            'activated' => 'required',
        ];
    }

    /**
     * Get custom validation messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'initialBalance.required' => 'O saldo inicial deve informado.',
            'initialBalanceDate.required' => 'Selecione o mês e o ano do saldo inicial.',
        ];
    }

    /**
     * Convert request to CardData DTO
     *
     * @throws UnknownProperties
     */
    public function accountData(): AccountData
    {
        return new AccountData(
            id: $this->input('id'),
            accountType: $this->input('accountType'),
            bankName: $this->input('bankName'),
            agencyNumber: $this->input('agencyNumber'),
            accountNumber: $this->input('accountNumber'),
            initialBalance: $this->input('initialBalance'),
            initialBalanceDate: $this->input('initialBalanceDate'),
            activated: $this->input('activated'),
        );
    }
}
