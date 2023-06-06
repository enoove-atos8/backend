<?php

namespace App\Application\Api\v1\Auth\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request*
     */
    public function toArray($request)
    {
        return [
            'token' => $this->token,
            'user'  =>  [
                'id'    =>  $this->id,
                'name'  =>  $this->name,
                'email' =>  $this->email,
                'avatar' =>  $this->avatar,
                'roles'  =>  [],
            ]
        ];
    }


    /**
     * @param $user
     * @return array
     */
    public function getRoles($user): array
    {
        $roles = [];

        foreach ($user->roles()->get() as $role)
        {
            $roles[] = [
                'id'           =>  $role->id,
                'name'         =>  $role->name,
                'description'  =>  $role->description,
                'activated'    =>  $role->activated,
                'abilities'    =>  $this->getAbilities($role),
            ];
        }

        return $roles;
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
