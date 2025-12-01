<?php

namespace Application\Api\v1\Secretary\Membership\Membership\Requests;

use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MemberRequest extends FormRequest
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
            'activated' => 'required',
            'deleted' => 'required',
            'personDataAndIdentification.avatar' => '',
            'personDataAndIdentification.fullName' => 'required',
            'personDataAndIdentification.gender' => 'required',
            'personDataAndIdentification.cpf' => ['nullable', Rule::unique('members', 'cpf')->ignore($this->id)],
            'personDataAndIdentification.rg' => ['nullable', Rule::unique('members', 'rg')->ignore($this->id)],
            'personDataAndIdentification.work' => '',
            'personDataAndIdentification.bornDate' => 'required',
            'addressAndContact.email' => '',
            'addressAndContact.phone' => '',
            'addressAndContact.cellPhone' => 'required',
            // 'addressAndContact.cellPhone'                             =>  ['required',Rule::unique('members', 'cell_phone')->ignore($this->id)],
            'addressAndContact.address' => 'required',
            'addressAndContact.district' => 'required',
            'addressAndContact.city' => 'required',
            'addressAndContact.uf' => 'required',
            'parentageAndMaritalStatus.maritalStatus' => '',
            'parentageAndMaritalStatus.spouse' => '',
            'parentageAndMaritalStatus.father' => '',
            'parentageAndMaritalStatus.mother' => 'required',
            'ecclesiasticalInformation.ecclesiasticalFunction' => '',
            'ecclesiasticalInformation.memberType' => 'required',
            'ecclesiasticalInformation.ministries' => '',
            'ecclesiasticalInformation.baptismDate' => '',
            'ecclesiasticalInformation.groupIds' => '',
            'otherInformation.bloodType' => '',
            'otherInformation.education' => '',
            'otherInformation.dependentsMembersIds' => ['nullable', 'array'],
            'otherInformation.dependentsMembersIds.*' => ['integer', 'exists:members,id'],
        ];
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [

        ];
    }

    /**
     * Function to data transfer objects to UserData class
     *
     * @throws UnknownProperties
     */
    public function memberData(): MemberData
    {
        return new MemberData(
            activated: $this->input('activated'),
            deleted: $this->input('deleted'),
            avatar: $this->input('personDataAndIdentification.avatar'),
            fullName: $this->input('personDataAndIdentification.fullName'),
            gender: $this->input('personDataAndIdentification.gender'),
            cpf: $this->input('personDataAndIdentification.cpf'),
            rg: $this->input('personDataAndIdentification.rg'),
            work: $this->input('personDataAndIdentification.work'),
            bornDate: $this->input('personDataAndIdentification.bornDate'),
            email: $this->input('addressAndContact.email'),
            phone: $this->input('addressAndContact.phone'),
            cellPhone: $this->input('addressAndContact.cellPhone'),
            address: $this->input('addressAndContact.address'),
            district: $this->input('addressAndContact.district'),
            city: $this->input('addressAndContact.city'),
            uf: $this->input('addressAndContact.uf'),
            maritalStatus: $this->input('parentageAndMaritalStatus.maritalStatus'),
            spouse: $this->input('parentageAndMaritalStatus.spouse'),
            father: $this->input('parentageAndMaritalStatus.father'),
            mother: $this->input('parentageAndMaritalStatus.mother'),
            ecclesiasticalFunction: $this->input('ecclesiasticalInformation.ecclesiasticalFunction'),
            memberType: $this->input('ecclesiasticalInformation.memberType'),
            ministries: $this->input('ecclesiasticalInformation.ministries'),
            baptismDate: $this->input('ecclesiasticalInformation.baptismDate'),
            groupIds: $this->input('ecclesiasticalInformation.groupIds'),
            bloodType: $this->input('otherInformation.bloodType'),
            education: $this->input('otherInformation.education'),
            dependentsMembersIds: $this->input('otherInformation.dependentsMembersIds'),
        );
    }
}
