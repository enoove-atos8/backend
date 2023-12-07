<?php

namespace Application\Api\v1\Members\Requests;

use Domain\Members\DataTransferObjects\MemberData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MemberRequest extends FormRequest
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
            'activated'                                               =>  'required|integer',
            'deleted'                                                 =>  'required|integer',
            'personDataAndIdentification.avatar'                      =>  '',
            'personDataAndIdentification.fullName'                    =>  'required',
            'personDataAndIdentification.gender'                      =>  'required',
            'personDataAndIdentification.cpf'                         =>  ['required',Rule::unique('members', 'cpf')->ignore($this->id)],
            'personDataAndIdentification.rg'                          =>  ['required',Rule::unique('members', 'rg')->ignore($this->id)],
            'personDataAndIdentification.work'                        =>  'required',
            'personDataAndIdentification.bornDate'                    =>  'required',
            'addressAndContact.email'                                 =>  ['required', 'email', Rule::unique('members', 'email')->ignore($this->id)],
            'addressAndContact.phone'                                 =>  '',
            'addressAndContact.cellPhone'                             =>  ['required',Rule::unique('members', 'cellPhone')->ignore($this->id)],
            'addressAndContact.address'                               =>  'required',
            'addressAndContact.district'                              =>  'required',
            'addressAndContact.city'                                  =>  'required',
            'addressAndContact.uf'                                    =>  'required',
            'parentageAndMaritalStatus.maritalStatus'                 =>  'required',
            'parentageAndMaritalStatus.spouse'                        =>  '',
            'parentageAndMaritalStatus.father'                        =>  'required',
            'parentageAndMaritalStatus.mother'                        =>  'required',
            'ecclesiasticalInformation.ecclesiasticalFunction'        =>  'required',
            'ecclesiasticalInformation.ministries'                    =>  '',
            'ecclesiasticalInformation.baptismDate'                   =>  'required',
            'otherInformation.bloodType'                              =>  'required',
            'otherInformation.education'                              =>  'required',
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

        ];
    }

    /**
     * Function to data transfer objects to UserData class
     *
     * @return MemberData
     * @throws UnknownProperties
     */
    public function memberData(): MemberData
    {
        return new MemberData(
            activated:                  $this->input('activated'),
            deleted:                    $this->input('deleted'),
            avatar:                     $this->input('personDataAndIdentification.avatar'),
            fullName:                   $this->input('personDataAndIdentification.fullName'),
            gender:                     $this->input('personDataAndIdentification.gender'),
            cpf:                        $this->input('personDataAndIdentification.cpf'),
            rg:                         $this->input('personDataAndIdentification.rg'),
            work:                       $this->input('personDataAndIdentification.work'),
            bornDate:                   $this->input('personDataAndIdentification.bornDate'),
            email:                      $this->input('addressAndContact.email'),
            phone:                      $this->input('addressAndContact.phone'),
            cellPhone:                  $this->input('addressAndContact.cellPhone'),
            address:                    $this->input('addressAndContact.address'),
            district:                   $this->input('addressAndContact.district'),
            city:                       $this->input('addressAndContact.city'),
            uf:                         $this->input('addressAndContact.uf'),
            maritalStatus:              $this->input('parentageAndMaritalStatus.maritalStatus'),
            spouse:                     $this->input('parentageAndMaritalStatus.spouse'),
            father:                     $this->input('parentageAndMaritalStatus.father'),
            mother:                     $this->input('parentageAndMaritalStatus.mother'),
            ecclesiasticalFunction:     $this->input('ecclesiasticalInformation.ecclesiasticalFunction'),
            ministries:                 $this->input('ecclesiasticalInformation.ministries'),
            baptismDate:                $this->input('ecclesiasticalInformation.baptismDate'),
            bloodType:                  $this->input('otherInformation.bloodType'),
            education:                  $this->input('otherInformation.education'),

        );
    }
}
