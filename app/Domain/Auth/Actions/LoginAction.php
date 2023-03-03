<?php

namespace Domain\Auth\Actions;

use Domain\Auth\DataTransferObjects\AuthData;
use Illuminate\Support\Facades\Auth;
use Infrastructure\Traits\Roles\HasAuthorization;
use Domain\Users\SubDomains\Roles\Models\Role;
use Domain\Users\SubDomains\Abilities\Models\Ability;


class LoginAction
{
    use HasAuthorization;

    public function __invoke(AuthData $authData, $app_name)
    {
        if(Auth::attempt($authData->toArray()))
        {
            $user = auth()->user();

            $userAbilities = $this->getAbilities($user);

            if ($user->activated)
            {
                $token = $user->createToken($app_name, $userAbilities)->plainTextToken;
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
