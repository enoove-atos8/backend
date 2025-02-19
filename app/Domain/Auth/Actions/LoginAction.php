<?php

namespace Domain\Auth\Actions;

use Domain\Auth\DataTransferObjects\AuthData;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Infrastructure\Traits\Roles\HasAuthorization;
use Throwable;


class LoginAction
{
    use HasAuthorization;

    private \Domain\CentralDomain\Churches\Church\Actions\GetChurchAction $getChurchAction;


    public function __construct(\Domain\CentralDomain\Churches\Church\Actions\GetChurchAction $getChurchAction)
    {
        $this->getChurchAction = $getChurchAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(AuthData $authData, string $tenantId): array|Authenticatable|null
    {
        if(Auth::attempt($authData->toArray()))
        {
            $user = auth()->user();
            $userDetail = $user->detail()->first();
            $userRoles = [];

            foreach ($user->roles()->get() as $role)
                $userRoles [] = $role->name;

            $church = $this->getChurchAction->execute($tenantId);

            if ($user->activated)
            {
                $token = $user->createToken('web', $userRoles)->plainTextToken;
                $user->token = $token;

                return [
                    'user'          => $user,
                    'userDetail'    => $userDetail,
                    'church'        => $church
                ];
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
