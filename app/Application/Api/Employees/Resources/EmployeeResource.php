<?php

namespace Application\Api\Employees\Resources;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EmployeeResource extends JsonResource
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'data';


    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $roles = $this['user']->roles()->first() ? $this['user']['roles'] : null;
        //$abilities = $roles->abilities()->get();

        return [
            'user'  => [
                'id'        =>  $this['user']['id'],
                'email'     =>  $this['user']['email'],
                'activated' =>  $this['user']['activated'],
                'role'  =>  [
                    'id'    =>  $roles->id,
                    'name'  =>  $roles->name,
                    'type'  =>  $roles->type,
                    //'abilities' =>  $abilities
                ] ? $roles != null : [],
            ],
            'employee'  =>  [
                'first_name'    =>  $this['employee']['first_name'],
                'last_name'     =>  $this['employee']['last_name'],
                'gender'        =>  $this['employee']['gender'],
                'birth_date'    =>  $this['employee']['birth_date'],
                'cpf'           =>  $this['employee']['cpf'],
                'rg'            =>  $this['employee']['rg'],
                'cell_phone'    =>  $this['employee']['cell_phone'],
            ]
        ];
    }

    public function with($request): array
    {
        return [

        ];
    }
}
