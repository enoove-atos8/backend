<?php

namespace App\Domain\Accounts\TenantVerification\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;

class GetTenantByUserAction
{
    public function __construct()
    {
    }


    /**
     * @param string $email
     * @return Model
     */
    public function execute(string $email): Model
    {

    }
}
