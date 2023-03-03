<?php

namespace Domain\Auth\Actions;

class LogoutAction
{
    public function __invoke()
    {
        return auth()->user()->currentAccessToken()->delete();
    }
}
