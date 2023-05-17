<?php

namespace Domain\Users\Actions;

use Infrastructure\Traits\Roles\HasAuthorization;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ListUserAction
{
    use HasAuthorization;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function __invoke($id = null)
    {
        if($this->hasRole(auth()->user(), ['admin']))
        {
            if ($id)
                return $this->user->find($id);
            else
                return $this->user->all();
            //return User::paginate();
        }
        else
        {
            return [
                "data"  =>  false,
                "status"  =>  403
            ];
        }
    }
}
