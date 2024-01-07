<?php

namespace Application\Api\v1\Members\Resources;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class MemberResource extends JsonResource
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'member';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $member = $this->resource;

        $ministries = [];
        $ecclesiasticalFunction = [];

        return [
            'id'                  =>  $member->id,
            'activated'           =>  $member->activated,
            'deleted'             =>  $member->deleted,
            'personDataAndIdentification' => [
                'avatar'        => $member->avatar,
                'fullName'      => $member->full_name,
                'gender'        => $member->gender,
                'cpf'           => $member->cpf,
                'rg'            => $member->rg,
                'work'          => $member->work,
                'bornDate'      => $member->born_date,
            ],
            'addressAndContact' => [
                'email'         => $member->email,
                'phone'         => $member->phone,
                'cellPhone'     => $member->cell_phone,
                'address'       => $member->address,
                'district'      => $member->district,
                'city'          => $member->city,
                'uf'            => $member->uf,
            ],
            'parentageAndMaritalStatus' => [
                'maritalStatus'  => $member->marital_status,
                'spouse'         => $member->spouse,
                'father'         => $member->father,
                'mother'         => $member->mother,
            ],
            'ecclesiasticalInformation' => [
                'ecclesiasticalFunction'    => $ecclesiasticalFunction,
                'ministries'                => $ministries,
                'baptismDate'               => $member->baptism_date,
            ],
            'otherInformation' => [
                'bloodType'         => $member->blood_type,
                'education'         => $member->education,
            ]
        ];
    }


    public function with($request): array
    {
        return [];
    }
}
