<?php

namespace Domain\Auth\Actions;

class LogoutAction
{
    public function execute()
    {
        return auth()->user()->currentAccessToken()->delete();
    }
}
