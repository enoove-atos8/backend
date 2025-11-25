<?php

namespace Domain\Auth\Actions;

use Domain\Auth\DataTransferObjects\AuthData;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Infrastructure\Traits\Roles\HasAuthorization;
use Throwable;

class LoginFromAppAction
{
    use HasAuthorization;

    private GetChurchAction $getChurchAction;
    private GetChurchesAction $getChurchesAction;


    public function __construct(
        GetChurchAction     $getChurchAction,
        GetChurchesAction   $getChurchesAction
    )
    {
        $this->getChurchAction = $getChurchAction;
        $this->getChurchesAction = $getChurchesAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(AuthData $authData): array|Authenticatable|null
    {
        $churches = $this->getChurchesAction->execute();
        $tenantOfUser = null;


        foreach ($churches as $church)
        {
            tenancy()->initialize($church->tenantId);

            if(Auth::attempt($authData->toArray()))
            {
                $user = auth()->user();
                $userDetail = $user->detail()->first();
                $userRoles = [];

                foreach ($user->roles()->get() as $role)
                    $userRoles [] = $role->name;

                $church = $this->getChurchAction->execute($church->tenantId);

                if ($user->activated)
                {
                    $token = $user->createToken('app', $userRoles)->plainTextToken;
                    $user->token = $token;
                    $tenantOfUser = $church->tenantId;

                    return [
                        'user'          => $user,
                        'userDetail'    => $userDetail,
                        'church'        => $church
                    ];
                }
                else
                    return ["error"  =>  false,"status"  =>  401];
            }
        }

        if(is_null($tenantOfUser))
            return ["error"  =>  false,"status"  =>  404];

    }
}
