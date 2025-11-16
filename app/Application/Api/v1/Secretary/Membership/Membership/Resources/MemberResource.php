<?php

namespace Application\Api\v1\Secretary\Membership\Membership\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     *
     * @var string
     */
    public static $wrap = 'member';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        $member = $this->resource;

        $ministries = [];
        $ecclesiasticalFunction = [];

        return [
            'id' => $member->id,
            'activated' => $member->activated,
            'deleted' => $member->deleted,
            'personDataAndIdentification' => [
                'avatar' => $member->avatar,
                'fullName' => $member->fullName,
                'memberType' => $member->memberType,
                'gender' => $member->gender,
                'cpf' => $member->cpf,
                'rg' => $member->rg,
                'work' => $member->work,
                'bornDate' => $member->bornDate,
            ],
            'addressAndContact' => [
                'email' => $member->email,
                'phone' => $member->phone,
                'cellPhone' => $member->cellPhone,
                'address' => $member->address,
                'district' => $member->district,
                'city' => $member->city,
                'uf' => $member->uf,
            ],
            'parentageAndMaritalStatus' => [
                'maritalStatus' => $member->maritalStatus,
                'spouse' => $member->spouse,
                'father' => $member->father,
                'mother' => $member->mother,
            ],
            'ecclesiasticalInformation' => [
                'ecclesiasticalFunction' => $ecclesiasticalFunction,
                'ministries' => $ministries,
                'memberType' => $member->memberType,
                'baptismDate' => $member->baptismDate,
                'groupIds' => $member->groupIds,
            ],
            'otherInformation' => [
                'bloodType' => $member->bloodType,
                'education' => $member->education,
            ],
            'titheHistory' => $member->titheHistory ?? [],
        ];
    }

    public function with($request): array
    {
        return [];
    }
}
