<?php

namespace Application\Api\v1\Members\Resources;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;
use function Webmozart\Assert\Tests\StaticAnalysis\length;

class MemberResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'members';


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($item){

            $ministries = [];
            $ecclesiasticalFunction = [];


            return [
                'id'                  =>  $item->id,
                'activated'           =>  $item->activated,
                'deleted'             =>  $item->deleted,
                'personDataAndIdentification' => [
                    'avatar'        => $item->avatar,
                    'fullName'      => $item->full_name,
                    'gender'        => $item->gender,
                    'cpf'           => $item->cpf,
                    'rg'            => $item->rg,
                    'work'          => $item->work,
                    'bornDate'      => $item->born_date,
                ],
                'addressAndContact' => [
                    'email'         => $item->email,
                    'phone'         => $item->phone,
                    'cellPhone'     => $item->cell_phone,
                    'address'       => $item->address,
                    'district'      => $item->district,
                    'city'          => $item->city,
                    'uf'            => $item->uf,
                ],
                'parentageAndMaritalStatus' => [
                    'maritalStatus'  => $item->marital_status,
                    'spouse'         => $item->spouse,
                    'father'         => $item->father,
                    'mother'         => $item->mother,
                ],
                'ecclesiasticalInformation' => [
                    'ecclesiasticalFunction'    => $ecclesiasticalFunction,
                    'ministries'                => $ministries,
                    'baptismDate'               => $item->baptism_date,
                ],
                'otherInformation' => [
                    'bloodType'         => $item->blood_type,
                    'education'         => $item->education,
                ]
            ];
        });
    }



    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
