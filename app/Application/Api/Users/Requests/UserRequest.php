<?php

namespace Application\Api\Users\Requests;

use Domain\Users\DataTransferObjects\UserData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserRequest extends FormRequest
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
    public function rules()
    {
        return [
            'email'             =>  'required|email|unique:users',
            'password'          =>  'required|string|min:6',
            'confirm_password'  =>  'required|same:password',
            'type'              =>  'required',
            'roles'             =>  'array|required',
        ];
    }


    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'            => 'O Preenchimento do campo email é obrigatório',
            'email.unique'              => 'Este email já esta cadastrado, verifique!',
            'password.required'         => 'O Preenchimento do campo senha é obrigatório',
            'password.min'              => 'A senha deve ter no mínimo 6 caracteres, verifique!',
            'confirm_password.same'     => 'As senhas informadas devem ser iguais, verifique!',
            'type.required'             => 'O Preenchimento do campo type é obrigatório',
            'roles.required'            => 'O Preenchimento do campo roles é obrigatório',
            'roles.array'               => 'O formato dos dados passado é inválido!',
        ];
    }

    /**
     * Function to data transfer objects to UserData class
     *
     * @return UserData
     * @throws UnknownProperties
     */
    public function userData(): UserData
    {
        return new UserData(
            email:      $this->input('email'),
            password:   $this->input('password'),
            type:   $this->input('type'),
            roles:      $this->input('roles'),
        );
    }
}
