<?php

namespace Application\Api\v1\Secretary\Membership\Membership\Requests;

use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Illuminate\Foundation\Http\FormRequest;

class BatchMemberRequest extends FormRequest
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
            'members' => 'required|array|min:1',
            'members.*.activated' => 'required',
            'members.*.deleted' => 'required',
            'members.*.personDataAndIdentification.avatar' => '',
            'members.*.personDataAndIdentification.fullName' => 'required',
            'members.*.personDataAndIdentification.gender' => 'required',
            'members.*.personDataAndIdentification.cpf' => 'nullable',
            'members.*.personDataAndIdentification.rg' => 'nullable',
            'members.*.personDataAndIdentification.work' => '',
            'members.*.personDataAndIdentification.bornDate' => 'required',
            'members.*.addressAndContact.email' => '',
            'members.*.addressAndContact.phone' => '',
            'members.*.addressAndContact.cellPhone' => 'required',
            'members.*.addressAndContact.address' => 'required',
            'members.*.addressAndContact.district' => 'required',
            'members.*.addressAndContact.city' => 'required',
            'members.*.addressAndContact.uf' => 'required',
            'members.*.parentageAndMaritalStatus.maritalStatus' => '',
            'members.*.parentageAndMaritalStatus.spouse' => '',
            'members.*.parentageAndMaritalStatus.father' => '',
            'members.*.parentageAndMaritalStatus.mother' => 'required',
            'members.*.ecclesiasticalInformation.ecclesiasticalFunction' => '',
            'members.*.ecclesiasticalInformation.memberType' => 'required',
            'members.*.ecclesiasticalInformation.ministries' => '',
            'members.*.ecclesiasticalInformation.baptismDate' => '',
            'members.*.ecclesiasticalInformation.groupIds' => '',
            'members.*.otherInformation.bloodType' => '',
            'members.*.otherInformation.education' => '',
            'members.*.otherInformation.dependentsMembersIds' => ['nullable', 'array'],
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
     * Convert request data to array of MemberData
     *
     * @return MemberData[]
     */
    public function membersData(): array
    {
        $members = $this->input('members', []);

        return array_map(function ($member) {
            return new MemberData(
                activated: $member['activated'] ?? 1,
                deleted: $member['deleted'] ?? 0,
                avatar: $member['personDataAndIdentification']['avatar'] ?? null,
                fullName: $member['personDataAndIdentification']['fullName'] ?? null,
                gender: $member['personDataAndIdentification']['gender'] ?? null,
                cpf: $member['personDataAndIdentification']['cpf'] ?? null,
                rg: $member['personDataAndIdentification']['rg'] ?? null,
                work: $member['personDataAndIdentification']['work'] ?? null,
                bornDate: $member['personDataAndIdentification']['bornDate'] ?? null,
                email: $member['addressAndContact']['email'] ?? null,
                phone: $member['addressAndContact']['phone'] ?? null,
                cellPhone: $member['addressAndContact']['cellPhone'] ?? null,
                address: $member['addressAndContact']['address'] ?? null,
                district: $member['addressAndContact']['district'] ?? null,
                city: $member['addressAndContact']['city'] ?? null,
                uf: $member['addressAndContact']['uf'] ?? null,
                maritalStatus: $member['parentageAndMaritalStatus']['maritalStatus'] ?? null,
                spouse: $member['parentageAndMaritalStatus']['spouse'] ?? null,
                father: $member['parentageAndMaritalStatus']['father'] ?? null,
                mother: $member['parentageAndMaritalStatus']['mother'] ?? null,
                ecclesiasticalFunction: $member['ecclesiasticalInformation']['ecclesiasticalFunction'] ?? null,
                memberType: $member['ecclesiasticalInformation']['memberType'] ?? null,
                ministries: $member['ecclesiasticalInformation']['ministries'] ?? null,
                baptismDate: $member['ecclesiasticalInformation']['baptismDate'] ?? null,
                groupIds: $member['ecclesiasticalInformation']['groupIds'] ?? null,
                bloodType: $member['otherInformation']['bloodType'] ?? null,
                education: $member['otherInformation']['education'] ?? null,
                dependentsMembersIds: $member['otherInformation']['dependentsMembersIds'] ?? null,
            );
        }, $members);
    }
}
