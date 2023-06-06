<?php

namespace Domain\Auth\Actions;

use Domain\Auth\DataTransferObjects\AuthData;
use Illuminate\Support\Facades\Auth;
use Infrastructure\Traits\Roles\HasAuthorization;


class LoginAction
{
    use HasAuthorization;

    public function __invoke(AuthData $authData)
    {
        if(Auth::attempt($authData->toArray()))
        {
            $user = auth()->user();

            if ($user->activated)
            {
                $token = $user->createToken('web')->plainTextToken;
                $user->token = $token;

                return $user;
            }
            else
                return ["error"  =>  false,"status"  =>  401];
        }
        else
            return ["error"  =>  false,"status"  =>  404];
    }

    public function getAbilities($user): array
    {
        $abilities = [];
        $userRole = $user->roles()->first();

        if($userRole != null){

            $userAbilities = $userRole->abilities()->get();

            foreach ($userAbilities as $ability)
                $abilities[] = $userRole->name . ":" . $ability["name"];
        }

        return $abilities;
    }
}
