<?php

namespace Application\Api\v1\Members\Resources;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
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
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($item){

            $ministries = [];
            $ecclesiasticalFunction = [];

            /*if(count($item->ministries) > 0)
            {
                foreach ($item->ministries as $ministry)
                {
                    $ministries [] = [

                    ];
                }
            }

            if(count($item->ecclesiasticalFunction) > 0)
            {
                foreach ($item->ecclesiasticalFunction as $function)
                {
                    $ecclesiasticalFunction [] = [

                    ];
                }
            }*/


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



    /**
     * @param mixed $detail
     * @return array
     */
    public function mountUserDetailsArray(mixed $detail): array
    {
        $result = [];
        if($detail != null)
        {
            $result =  [
                'user_id'       =>  $detail->user_id,
                'fullName'     =>  $detail->full_name,
                'avatar'        =>  $detail->avatar,
                'type'          =>  $detail->type,
                'title'         =>  $detail->title,
                'gender'        =>  $detail->gender,
                'phone'         =>  $detail->phone,
                'address'       =>  $detail->address,
                'district'      =>  $detail->district,
                'city'          =>  $detail->city,
                'country'       =>  $detail->country,
                'birthday'      =>  $detail->birthday,
            ];
        }

        return $result;
    }



    /**
     * @param mixed $roles
     * @return array
     */
    public function mountUserRolesArray(mixed $roles): array
    {
        $tempRoles = [];
        if($roles != null)
        {
            foreach ($roles as $role)
            {
                $tempRoles [] = [
                    'id'            =>  $role->id,
                    'name'          =>  $role->name,
                    'guardName'    =>  $role->guard_name,
                    'displayName'  =>  $role->display_name,
                    'description'   =>  $role->description,
                    'activated'     =>  $role->activated,
                ];
            }
        }

        return $tempRoles;
    }



    public function with($request): array
    {
        return [
            'total' => count($this)
        ];
    }
}
