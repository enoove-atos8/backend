<?php

namespace App\Domain\Notifications\Actions\User;


use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Notifications\Mail\User\NewUserMail;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NewUserNotificationAction
{

    public function __construct(
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(UserData $userData, UserDetailData $userDetailData, string $tenant): void
    {
        Mail::to($userData->email)->send(new NewUserMail($userData, $userDetailData, $tenant));
    }
}
