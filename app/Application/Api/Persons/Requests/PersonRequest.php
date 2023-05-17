<?php

namespace Application\Api\Persons\Requests;

use Domain\Persons\DataTransferObjects\PersonData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PersonRequest extends FormRequest
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

        ];
    }

    /**
     * Function to data transfer objects to UserData class
     *
     * @return PersonData
     * @throws UnknownProperties
     */
    public function personData(): PersonData
    {
        return new PersonData(
            userId:         $this->input('userId'),
            firstName:      $this->input('firstName'),
            lastName:       $this->input('lastName'),
            avatar:         $this->input('avatar'),
            gender:         $this->input('gender'),
            birthDate:      $this->input('birthDate'),
            cpf:            $this->input('cpf'),
            rg:             $this->input('rg'),
            cellPhone:      $this->input('cellPhone'),
            ministry:       $this->input('ministry'),
            department:     $this->input('department'),
            responsibility: $this->input('responsibility'),
        );
    }
}
