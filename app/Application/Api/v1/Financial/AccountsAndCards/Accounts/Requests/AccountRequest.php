<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Accounts\Requests;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id'             => '',
            'accountType'    => 'required',
            'bankName'       => 'required',
            'agencyNumber'   => 'required',
            'accountNumber'  => 'required',
            'activated'      => 'required',
        ];
    }

    /**
     * Convert request to CardData DTO
     *
     * @return AccountData
     * @throws UnknownProperties
     */
    public function accountData(): AccountData
    {
        return new AccountData(
            id:             $this->input('id'),
            accountType:    $this->input('accountType'),
            bankName:       $this->input('bankName'),
            agencyNumber:   $this->input('agencyNumber'),
            accountNumber:  $this->input('accountNumber'),
            activated:      $this->input('activated'),
        );
    }
}
