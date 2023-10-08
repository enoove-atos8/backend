<?php

namespace Application\Api\v1\Users\Resources;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $user = $this->resource;
        $roles = null;
        $userDetail = null;
        //if(count($user->detail()->get()) > 0) $userDetail = $user->detail()->first();
        //if(count($user->roles()->get()) > 0) $roles = $user->roles()->first();

        return [
            'id'                    =>  $user->id,
            'email'                 =>  $user->email,
            'activated'             =>  $user->activated,
            'type'                  =>  $user->type,
            'changedPassword'       =>  $user->changedPassword,
            'accessQuantity'        =>  $user->accessQuantity,
        ];
    }

    public function with($request)
    {
        return [];
    }
}
