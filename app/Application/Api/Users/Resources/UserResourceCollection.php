<?php

namespace Application\Api\Users\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'users';


    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return $this->collection->map(function ($item){

            if (count($item->roles()->get()) > 0)
                $userRole = $item->roles()->first();
            else
                $userRole = null;

            $valuesRole = [];
            $abilities = [];

            if (!is_null($userRole))
            {
                $valuesRole[] = ['id' => $userRole->id, 'name' => $userRole->name];

                foreach ($userRole->abilities()->get() as $ability)
                    $abilities[] = ['id' => $ability["id"], 'name' => $ability["name"]];
            }

            return [
                'id'    => $item->id,
                'name'  => $item->name,
                'email' => $item->email,
                'date'  =>  Carbon::create($item->created_at)->format('Y-m-d'),
                'roles'  =>  [
                    $valuesRole,
                    'abilities' => $abilities
                ]
            ];
        });
    }


    public function with($request)
    {
        return [
            'total' => count($this)
        ];
    }
}
