<?php

namespace Application\Api\v1\Users\Resources;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
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
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($item){
            $roles = [];
            $detail = null;
            if(count($item->resource->roles()->get()) > 0)
                $roles = $item->resource->roles()->get();
            if(count($item->resource->detail()->get()) > 0)
                $detail = $item->resource->detail()->first();

            return [
                'id'                    =>  $item->id,
                'email'                 =>  $item->email,
                'activated'             =>  $item->activated,
                'type'                  =>  $item->type,
                'changedPassword'       =>  $item->changedPassword,
                'accessQuantity'        =>  $item->accessQuantity,
                'roles'                 =>  $this->mountUserRolesArray($roles),
                'details'               =>  $this->mountUserDetailsArray($detail)
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
