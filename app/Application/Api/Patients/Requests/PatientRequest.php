<?php

namespace Application\Api\Patients\Requests;

use Domain\Patients\DataTransferObjects\PatientData;
use Domain\Users\DataTransferObjects\UserData;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PatientRequest extends FormRequest
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
            'user_id'                   => 'required',
            'patient_responsible_id'    => 'required',
            'first_name'                => 'required|unique:patients|string|max:50',
            'last_name'                 => 'required|string|max:50',
            'birth_date'                => 'required|string',
            'cpf'                       => 'required|string',
            'rg'                        => 'required|string',
            'cell_phone'                => 'required|string',
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
     * Function to data transfer objects to PatientData class
     *
     * @return PatientData
     * @throws UnknownProperties
     */
    public function patientData(): PatientData
    {

        return new PatientData(
            user_id:                $this->input('user_id'),
            patient_responsible_id: $this->input('patient_responsible_id'),
            firstName:              $this->input('first_name'),
            lastName:               $this->input('last_name'),
            birth_date:             $this->input('birth_date'),
            cpf:                    $this->input('cpf'),
            rg:                     $this->input('rg'),
            cell_phone:             $this->input('cell_phone'),
        );
    }
}
