<?php

namespace App\Application\Api\v1\Auth\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request*
     */
    public function toArray($request): array
    {
        $user = $this->resource;
        $roles = [];
        $detail = null;
        if(count($user->roles()->get()) > 0)
            $roles = $user->roles()->get();
        if(count($user->detail()->get()) > 0)
            $detail = $user->detail()->first();

        return [
            'token' => $this->token,
            'user'  =>  [
                'id'                    =>  $user->id,
                'email'                 =>  $user->email,
                'activated'             =>  $user->activated,
                'type'                  =>  $user->type,
                'changedPassword'       =>  $user->changedPassword,
                'accessQuantity'        =>  $user->accessQuantity,
                'roles'                 =>  $this->mountUserRolesArray($roles),
                'details'               =>  $this->mountUserDetailsArray($detail)
            ]
        ];
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


    /**
     * @param $role
     * @return array
     */
    public function getAbilities($role): array
    {
        $abilities = [];

        foreach ($role->abilities()->get() as $ability)
        {
            $abilities[] = [
                'id'           =>  $ability->id,
                'name'         =>  $ability->name,
                'description'  =>  $ability->description,
                'activated'    =>  $ability->activated,
            ];
        }

        return $abilities;
    }
}
