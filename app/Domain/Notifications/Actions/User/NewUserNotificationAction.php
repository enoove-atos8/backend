<?php

namespace App\Domain\Notifications\Actions\User;


use App\Domain\Notifications\Mail\User\NewUserMail;
use Domain\Churches\DataTransferObjects\ChurchData;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\DataTransferObjects\UserDetailData;
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
