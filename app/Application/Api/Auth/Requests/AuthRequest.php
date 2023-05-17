<?php

namespace Application\Api\Auth\Requests;

use Domain\Auth\DataTransferObjects\AuthData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'email'             =>  'required|email',
            'password'          =>  'required'
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
            'email.required'      => 'Informe o email do usuário!',
            'email.email'         => 'Email inválido!',
            'password.required'   => 'Informe a senha do usuário!'
        ];
    }

    /**
     * Function to data transfer objects to UserData class
     *
     * @return AuthData
     * @throws UnknownProperties
     */
    public function authData(): AuthData
    {
        return new AuthData(
            email:    $this->input('email'),
            password: $this->input('password')
        );
    }
}
