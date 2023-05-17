<?php

namespace Infrastructure\Traits\Roles;

trait HasAuthorization
{
    /**
     * @param $authUser
     * @param array $roles
     * @return bool
     */
    public function hasRole($authUser, array $roles): bool
    {
        $userRole = $authUser->roles()->first();

        if($userRole != null)
        {
            foreach ($roles as $role)
                if($userRole->name == $role)
                    return true;
        }
        else
        {
            return false;
        }
    }


    /**
     * @param $authUser
     * @param string $ability
     * @return bool
     */
    public function hasAbility($authUser, string $ability): bool
    {
        $userRole = $authUser->roles()->first();
        return $userRole->abilities()->name == $ability;
    }
}
