<?php

namespace Application\Api\v1\Ecclesiastical\Divisions\Requests;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DivisionRequest extends FormRequest
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
            'slug'              =>  'required',
            'name'              =>  'required',
            'description'       =>  '',
            'requireLeader'     =>  '',
            'enabled'           =>  'required',
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
            'name.required'             =>  "O preenchimento do nome da divisão é obrigatório!",
            'slug.required'    =>  "O Slug deve ser enviado juntamente com os dados preenchidos, tente novamente mais tarde!",
            'enabled.required'          =>  "O campo enabled deve ser enviado juntamente com os dados preenchidos, tente novamente mais tarde!",
        ];
    }


    /**
     * Function to data transfer objects to DivisionData class
     *
     * @return DivisionData
     * @throws UnknownProperties
     */
    public function divisionData(): DivisionData
    {
        return new DivisionData(
            slug:               $this->input('slug'),
            name:               $this->input('name'),
            description:        $this->input('description'),
            requireLeader:      $this->input('requireLeader'),
            enabled:            $this->input('enabled'),
        );
    }
}
