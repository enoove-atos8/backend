<?php

namespace Application\Api\Employees\Requests;

use Domain\Employees\DataTransferObjects\EmployeeData;
use Domain\Users\DataTransferObjects\UserData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class EmployeeRequest extends FormRequest
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

            'user.email'                =>  'required|email|unique:users,email',
            'user.password'             =>  'required|string|min:6',
            'user.confirm_password'     =>  'required|same:user.password',
            'user.type'                 =>  'required',
            'user.roles'                =>  'array|required',
            'employee.first_name'       =>  'required',
            'employee.last_name'        =>  'required',
            'employee.gender'           =>  'required',
            'employee.birth_date'       =>  'required',
            'employee.cpf'              =>  'required',
            'employee.rg'               =>  'required',
            'employee.cell_phone'       =>  'required'
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
                'user.email.required'               => 'O Preenchimento do campo email é obrigatório',
                'user.email.unique'                 => 'Este email já esta cadastrado, verifique!',
                'user.password.required'            => 'O Preenchimento do campo senha é obrigatório',
                'user.password.min'                 => 'A senha deve ter no mínimo 6 caracteres, verifique!',
                'user.confirm_password.same'        => 'As senhas informadas devem ser iguais, verifique!',
                'user.type.required'                => 'O Preenchimento do campo type é obrigatório',
                'user.roles.required'               => 'O Preenchimento do campo roles é obrigatório',
                'user.roles.array'                  => 'O formato dos dados passado é inválido!',
                'employee.first_name.required'      => 'O Preenchimento do campo é obrigatório',
                'employee.last_name.required'       => 'O Preenchimento do campo é obrigatório',
                'employee.gender.required'          => 'O Preenchimento do campo é obrigatório',
                'employee.birth_date.required'      => 'O Preenchimento do campo é obrigatório',
                'employee.cpf.required'             => 'O Preenchimento do campo é obrigatório',
                'employee.rg.required'              => 'O Preenchimento do campo é obrigatório',
                'employee.cell_phone.required'      => 'O Preenchimento do campo é obrigatório',
        ];
    }

    /**
     * Function to data transfer objects to EmployeeData class
     *
     * @return array
     * @throws UnknownProperties
     */
    public function employeeData(): array
    {
        $userData = new UserData(
            email:      $this->input('user.email'),
            password:   $this->input('user.password'),
            type:       $this->input('user.type'),
            roles:      $this->input('user.roles'),
        );

        $employeeData = new EmployeeData(
            firstName:  $this->input('employee.first_name'),
            lastName:   $this->input('employee.last_name'),
            gender:     $this->input('employee.gender'),
            birthDate:  $this->input('employee.birth_date'),
            cpf:        $this->input('employee.cpf'),
            rg:         $this->input('employee.rg'),
            cellPhone:  $this->input('employee.cell_phone'),
        );

        return [
            'userData'      =>  $userData,
            'employeeData'  =>  $employeeData
        ];
    }
}
