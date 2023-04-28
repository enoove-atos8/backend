<?php

namespace Application\Api\Users\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'user';


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $userRole = $this->roles()->first();
        $valuesRole = [];
        $abilities = [];

        if($userRole != null){

            $valuesRole[] = ['id' => $userRole->id, 'name' => $userRole->name];

            foreach ($userRole->abilities()->get() as $ability)
                $abilities[] = ['id' => $ability["id"], 'name' => $ability["name"]];
        }


        return [
            'id' => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'register_date'  =>  Carbon::create($this->created_at)->format('d/m/Y'),
            'roles'  =>  [
                'values'    => $valuesRole,
                'abilities' => $abilities
            ]
        ];
    }

    public function with($request)
    {
        return [
            'status' => false
        ];
    }
}
